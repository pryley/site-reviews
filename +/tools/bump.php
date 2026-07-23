<?php

/*
 * Bumps the plugin version in the three files that carry it: composer.json
 * ("version"), readme.txt (Stable tag) and site-reviews.php (the Version
 * header). readme.txt's Stable tag is the source of truth (the Makefile's
 * VERSION reads it too).
 *
 *     php +/tools/bump.php [patch|minor|major|prerelease] [--dry-run]
 *
 * prerelease follows semver: 8.2.0 -> 8.2.1-0 -> 8.2.1-1 -> …
 */

$root = dirname(__DIR__, 2);
$args = array_slice($argv, 1);
$dryRun = in_array('--dry-run', $args, true);
$args = array_values(array_diff($args, ['--dry-run']));
$type = $args[0] ?? 'patch';
if (!in_array($type, ['patch', 'minor', 'major', 'prerelease'], true)) {
    fwrite(STDERR, "Unknown bump type [{$type}] — use patch, minor, major, or prerelease.\n");
    exit(1);
}

$readme = file_get_contents("{$root}/readme.txt");
if (!preg_match('/^Stable tag:\s*(\S+)/m', (string) $readme, $matches)) {
    fwrite(STDERR, "No Stable tag found in readme.txt.\n");
    exit(1);
}
$current = $matches[1];
if (!preg_match('/^(\d+)\.(\d+)\.(\d+)(?:-(\d+))?$/', $current, $parts)) {
    fwrite(STDERR, "Cannot parse the current version [{$current}].\n");
    exit(1);
}
[, $major, $minor, $patch] = $parts;
$pre = $parts[4] ?? null;

switch ($type) {
    case 'major':
        $next = sprintf('%d.0.0', $major + 1);
        break;
    case 'minor':
        $next = sprintf('%d.%d.0', $major, $minor + 1);
        break;
    case 'prerelease':
        $next = null === $pre
            ? sprintf('%d.%d.%d-0', $major, $minor, $patch + 1)
            : sprintf('%d.%d.%d-%d', $major, $minor, $patch, $pre + 1);
        break;
    default: // patch; a prerelease finalizes to its release version
        $next = null === $pre
            ? sprintf('%d.%d.%d', $major, $minor, $patch + 1)
            : sprintf('%d.%d.%d', $major, $minor, $patch);
        break;
}

$replacements = [
    'composer.json' => ['/("version":\s*")[^"]+(")/', '${1}'.$next.'${2}'],
    'readme.txt' => ['/^(Stable tag:\s*)\S+/m', '${1}'.$next],
    'site-reviews.php' => ['/^(\s*\*\s*Version:\s*)\S+/m', '${1}'.$next],
];
foreach ($replacements as $file => [$pattern, $replace]) {
    $path = "{$root}/{$file}";
    $contents = file_get_contents($path);
    $updated = preg_replace($pattern, $replace, (string) $contents, 1, $count);
    if (1 !== $count) {
        fwrite(STDERR, "No version key found in {$file} — nothing written.\n");
        exit(1);
    }
    if (!$dryRun) {
        file_put_contents($path, $updated);
    }
    printf("%s%s: %s -> %s\n", $dryRun ? '[dry-run] ' : '', $file, $current, $next);
}
