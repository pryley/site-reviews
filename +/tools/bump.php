<?php

/*
 * Bumps the plugin version in the three files that carry it: composer.json
 * ("version"), readme.txt (Stable tag) and site-reviews.php (the Version
 * header). readme.txt's Stable tag is the source of truth (the Makefile's
 * VERSION reads it too).
 *
 *     php +/tools/bump.php [patch|minor|major|prerelease|beta] [--dry-run]
 *
 * prerelease follows semver: 8.2.0 -> 8.2.1-0 -> 8.2.1-1 -> …
 * beta is the named flavour:  8.2.0 -> 8.2.1-beta1 -> 8.2.1-beta2 -> …
 * (a plain prerelease adopts the beta name at the same version: 8.2.1-0 ->
 * 8.2.1-beta1). patch finalizes either kind: 8.2.1-beta2 -> 8.2.1.
 */

$root = dirname(__DIR__, 2);
$args = array_slice($argv, 1);
$dryRun = in_array('--dry-run', $args, true);
$args = array_values(array_diff($args, ['--dry-run']));
$type = $args[0] ?? 'patch';
if (!in_array($type, ['patch', 'minor', 'major', 'prerelease', 'beta'], true)) {
    fwrite(STDERR, "Unknown bump type [{$type}] — use patch, minor, major, prerelease, or beta.\n");
    exit(1);
}

$readme = file_get_contents("{$root}/readme.txt");
if (!preg_match('/^Stable tag:\s*(\S+)/m', (string) $readme, $matches)) {
    fwrite(STDERR, "No Stable tag found in readme.txt.\n");
    exit(1);
}
$current = $matches[1];
if (!preg_match('/^(\d+)\.(\d+)\.(\d+)(?:-([a-z]*)(\d+))?$/', $current, $parts)) {
    fwrite(STDERR, "Cannot parse the current version [{$current}].\n");
    exit(1);
}
[, $major, $minor, $patch] = $parts;
$preLabel = isset($parts[5]) ? $parts[4] : null; // '' for a plain -N prerelease
$preNum = isset($parts[5]) ? (int) $parts[5] : null;

switch ($type) {
    case 'major':
        $next = sprintf('%d.0.0', $major + 1);
        break;
    case 'minor':
        $next = sprintf('%d.%d.0', $major, $minor + 1);
        break;
    case 'prerelease':
        $next = null === $preNum
            ? sprintf('%d.%d.%d-0', $major, $minor, $patch + 1)
            : sprintf('%d.%d.%d-%s%d', $major, $minor, $patch, $preLabel, $preNum + 1);
        break;
    case 'beta':
        if (null === $preNum) {
            $next = sprintf('%d.%d.%d-beta1', $major, $minor, $patch + 1);
        } elseif ('beta' === $preLabel) {
            $next = sprintf('%d.%d.%d-beta%d', $major, $minor, $patch, $preNum + 1);
        } else { // a plain -N prerelease adopts the beta name at the same version
            $next = sprintf('%d.%d.%d-beta1', $major, $minor, $patch);
        }
        break;
    default: // patch; a prerelease of either kind finalizes to its release version
        $next = null === $preNum
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
