<?php

/*
 * Driving the real analyser over a fixture is the only way to exercise the
 * rule: it reads a file's place from its path, so the caught file has to live
 * at .../plugin/Controllers/Api/.
 */

uses()->group('plugin');

function restPermissionFixture(array $files): string
{
    $dir = sys_get_temp_dir().'/glsr-restpermission-'.getmypid();
    exec(sprintf('rm -rf %s', escapeshellarg($dir)));
    foreach ($files as $relative => $contents) {
        $path = "{$dir}/{$relative}";
        is_dir(dirname($path)) || mkdir(dirname($path), 0755, true);
        file_put_contents($path, $contents);
    }
    file_put_contents("{$dir}/phpstan.neon", implode("\n", [
        'rules:',
        '  - GeminiLabs\SiteReviews\PhpStan\RestPermissionRule',
        'parameters:',
        '  level: 0',
        '  paths:',
        "    - {$dir}/plugin",
    ])."\n");
    return $dir;
}

function restPermissionErrors(string $dir): array
{
    $root = dirname(__DIR__, 3);
    exec(sprintf('cd %s && XDEBUG_MODE=off %s analyse -c %s -a %s --no-progress --error-format=json 2>/dev/null',
        escapeshellarg($root),
        escapeshellarg("{$root}/vendor/bin/phpstan"),
        escapeshellarg("{$dir}/phpstan.neon"),
        escapeshellarg("{$root}/vendor/autoload.php")
    ), $output);
    $report = json_decode(implode("\n", $output), true) ?: [];
    $errors = [];
    foreach ($report['files'] ?? [] as $file => $data) {
        foreach ($data['messages'] ?? [] as $message) {
            if ('siteReviews.restPermission' === ($message['identifier'] ?? '')) {
                $errors[basename($file)] = $message['message'];
            }
        }
    }
    return $errors;
}

test('hasPermission() is refused under the REST controllers and allowed elsewhere', function () {
    $probe = fn (string $namespace) => "<?php\n\nnamespace GeminiLabs\\SiteReviews\\{$namespace};\n\n"
        ."class Probe\n{\n    public function check(object \$command): bool\n    {\n"
        ."        return \$command->hasPermission();\n"
        ."    }\n}\n";
    $dir = restPermissionFixture([
        'plugin/Controllers/Api/Version1/Refused.php' => $probe('Controllers\\Api\\Version1'),
        'plugin/Controllers/Allowed.php' => $probe('Controllers'),
    ]);

    expect(restPermissionErrors($dir))->toBe([
        'Refused.php' => 'hasPermission() passes whenever is_admin() is false; a REST permission callback checks with glsr()->can().',
    ]);
});
