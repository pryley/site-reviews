<?php

/*
 * The rule is driven over a fixture by the real analyser, as RestPermissionRuleTest does: a route
 * whose permission_callback is __return_true is an error until its line says why it is public.
 */

uses()->group('plugin');

function publicRouteErrors(array $files): array
{
    $dir = sys_get_temp_dir().'/glsr-publicroute-'.getmypid();
    exec(sprintf('rm -rf %s', escapeshellarg($dir)));
    foreach ($files as $relative => $contents) {
        $path = "{$dir}/{$relative}";
        is_dir(dirname($path)) || mkdir(dirname($path), 0755, true);
        file_put_contents($path, $contents);
    }
    file_put_contents("{$dir}/phpstan.neon", implode("\n", [
        'rules:',
        '  - GeminiLabs\SiteReviews\PhpStan\PublicRouteRule',
        'parameters:',
        '  level: 0',
        '  paths:',
        "    - {$dir}/plugin",
    ])."\n");
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
            $errors[basename($file)][] = $message['identifier'] ?? '';
        }
    }
    ksort($errors);
    return $errors;
}

test('a route open to everyone is refused until its line says why', function () {
    $route = fn (string $callback, string $comment = '') => "<?php\n\nnamespace GeminiLabs\\SiteReviews\\Controllers;\n\n"
        ."class Probe\n{\n    public function routes(): array\n    {\n"
        ."        return [\n"
        ."            'callback' => 'render',\n"
        ."            'permission_callback' => '{$callback}',{$comment}\n"
        ."        ];\n"
        ."    }\n}\n";

    $errors = publicRouteErrors([
        'plugin/Controllers/Checked.php' => $route('checkPermission'),
        'plugin/Controllers/Explained.php' => $route('__return_true', ' // @phpstan-ignore siteReviews.publicRoute (a visitor reads it)'),
        'plugin/Controllers/Open.php' => $route('__return_true'),
    ]);

    expect($errors)->toBe(['Open.php' => ['siteReviews.publicRoute']]);
});
