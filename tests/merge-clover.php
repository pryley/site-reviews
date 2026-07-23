<?php

/*
 * Merges clover files into one: line counts are united per file (max count
 * wins — "covered anywhere is covered"). Both wp-env instances mount the
 * plugin at the same container path, so files line up by name.
 *
 *     php tests/merge-clover.php <in.xml>... <out.xml>
 *
 * Used by `make coverage:merge` to fold the multisite suite's coverage
 * (tests/coverage/multisite.xml) into the main run (tests/coverage/clover.xml),
 * and to print the merged per-file table — the view Pest itself cannot show,
 * since its console report only knows the run it made itself.
 *
 * BOTH inputs must be generated from the SAME code: a clover from before a
 * shipped-code change carries the old line numbering, and merging it with a
 * fresh one produces phantom uncovered lines where the numbers skewed.
 * Regenerate both after changing anything in plugin/.
 */

$args = array_slice($argv, 1);
if (count($args) < 2) {
    fwrite(STDERR, "Usage: php tests/merge-clover.php <in.xml>... <out.xml>\n");
    exit(1);
}
$output = array_pop($args);

$files = []; // name => ['lines' => [num => ['count' => int, 'type' => string]]]
foreach ($args as $clover) {
    if (!file_exists($clover)) {
        fwrite(STDERR, "Skipping missing clover file: {$clover}\n");
        continue;
    }
    $xml = simplexml_load_file($clover);
    if (false === $xml) {
        fwrite(STDERR, "Cannot parse: {$clover}\n");
        exit(1);
    }
    foreach ($xml->xpath('//file') as $file) {
        $name = (string) $file['name'];
        foreach ($file->line as $line) {
            $num = (int) $line['num'];
            $count = (int) $line['count'];
            $type = (string) $line['type'];
            $known = $files[$name]['lines'][$num]['count'] ?? -1;
            if ($count > $known) {
                $files[$name]['lines'][$num] = ['count' => $count, 'type' => $type ?: 'stmt'];
            }
        }
    }
}
if (empty($files)) {
    fwrite(STDERR, "Nothing to merge.\n");
    exit(1);
}

ksort($files);
$dom = new DOMDocument('1.0', 'UTF-8');
$dom->formatOutput = true;
$coverage = $dom->createElement('coverage');
$coverage->setAttribute('generated', (string) time());
$dom->appendChild($coverage);
$project = $dom->createElement('project');
$project->setAttribute('timestamp', (string) time());
$coverage->appendChild($project);

$totalStatements = 0;
$totalCovered = 0;
foreach ($files as $name => $data) {
    $fileEl = $dom->createElement('file');
    $fileEl->setAttribute('name', $name);
    ksort($data['lines']);
    $statements = 0;
    $covered = 0;
    foreach ($data['lines'] as $num => $line) {
        $lineEl = $dom->createElement('line');
        $lineEl->setAttribute('num', (string) $num);
        $lineEl->setAttribute('type', $line['type']);
        $lineEl->setAttribute('count', (string) $line['count']);
        $fileEl->appendChild($lineEl);
        if ('stmt' === $line['type']) {
            ++$statements;
            if ($line['count'] > 0) {
                ++$covered;
            }
        }
    }
    $metrics = $dom->createElement('metrics');
    $metrics->setAttribute('statements', (string) $statements);
    $metrics->setAttribute('coveredstatements', (string) $covered);
    $fileEl->appendChild($metrics);
    $project->appendChild($fileEl);
    $totalStatements += $statements;
    $totalCovered += $covered;
}
$metrics = $dom->createElement('metrics');
$metrics->setAttribute('statements', (string) $totalStatements);
$metrics->setAttribute('coveredstatements', (string) $totalCovered);
$project->appendChild($metrics);

$dom->save($output);

/*
 * The merged table, in the shape of Pest's own coverage report: one row per
 * file (named relative to plugin/), the uncovered lines as ranges, and the
 * percentage — so "covered anywhere" is readable, not just written to disk.
 */

/**
 * Consecutive line numbers as ranges: [44,45,46,50] => "44..46, 50".
 *
 * @param int[] $lines
 */
function formatRanges(array $lines): string
{
    $ranges = [];
    $start = $end = null;
    foreach ($lines as $num) {
        if (null !== $end && $num === $end + 1) {
            $end = $num;
            continue;
        }
        if (null !== $start) {
            $ranges[] = $start === $end ? (string) $start : "{$start}..{$end}";
        }
        $start = $end = $num;
    }
    if (null !== $start) {
        $ranges[] = $start === $end ? (string) $start : "{$start}..{$end}";
    }
    return implode(', ', $ranges);
}

$width = 78;
echo "\nMerged coverage (covered in any suite counts):\n\n";
foreach ($files as $name => $data) {
    $statements = 0;
    $covered = 0;
    $uncovered = [];
    ksort($data['lines']); // the writer loop sorted a by-value copy, not this one
    foreach ($data['lines'] as $num => $line) {
        if ('stmt' !== $line['type']) {
            continue;
        }
        ++$statements;
        if ($line['count'] > 0) {
            ++$covered;
        } else {
            $uncovered[] = $num;
        }
    }
    $label = preg_replace('{^.*?/plugin/}', '', $name);
    $label = preg_replace('/\.php$/', '', $label);
    $pct = $statements ? 100 * $covered / $statements : 100;
    $right = empty($uncovered)
        ? sprintf('%.1f%%', $pct)
        : sprintf('%s / %.1f%%', formatRanges($uncovered), $pct);
    $dots = max(1, $width - strlen($label) - strlen($right) - 2);
    printf("  %s %s %s\n", $label, str_repeat('.', $dots), $right);
}
$total = sprintf('Total: %.1f %%', $totalStatements ? 100 * $totalCovered / $totalStatements : 0);
printf(
    "%s\n%s\n\nMerged %d file(s) into %s (%d/%d statements)\n",
    str_repeat('─', $width + 4),
    str_pad($total, $width + 2, ' ', STR_PAD_LEFT),
    count($files),
    $output,
    $totalCovered,
    $totalStatements
);
