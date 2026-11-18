<?php

/*
 * The rule is driven over fixtures by the real analyser, as PublicRouteRuleTest does: a statement
 * given to sql() is an error when a variable or a property is written into it.
 */

uses()->group('plugin');

function querySqlErrors(array $bodies): array
{
    $dir = sys_get_temp_dir().'/glsr-querysql-'.getmypid();
    exec(sprintf('rm -rf %s', escapeshellarg($dir)));
    mkdir("{$dir}/plugin", 0755, true);
    foreach ($bodies as $class => $body) {
        file_put_contents("{$dir}/plugin/{$class}.php", "<?php\n\nnamespace GeminiLabs\\SiteReviews\\Probes;\n\n"
            ."class {$class}\n{\n    public string \$tablename = 'wp_probe';\n\n"
            ."    public function run(string \$value, array \$ids): string\n    {\n{$body}\n    }\n\n"
            ."    public function sql(string \$statement, ...\$args): string\n    {\n        return \$statement;\n    }\n\n"
            ."    public function sqlHandle(): string\n    {\n        return 'probe';\n    }\n\n"
            ."    public function sqlLimit(): string\n    {\n        return 'LIMIT 1';\n    }\n}\n");
    }
    file_put_contents("{$dir}/phpstan.neon", implode("\n", [
        'rules:',
        '  - GeminiLabs\SiteReviews\PhpStan\QuerySqlRule',
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

test('a statement given to sql() is refused when a variable or a property is written into it', function () {
    $errors = querySqlErrors([
        // refused
        'Built' => '        $sql = "SELECT * FROM table|ratings WHERE name = \'{$value}\'";'."\n".'        return $this->sql($sql);',
        'Property' => '        return $this->sql("SELECT * FROM {$this->tablename}");',
        'Variable' => '        return $this->sql("SELECT * FROM table|ratings WHERE name = \'{$value}\'");',
        // allowed
        'Clause' => '        return $this->sql("SELECT * FROM table|ratings {$this->sqlLimit()}");',
        'Explained' => '        // @phpstan-ignore siteReviews.querySql (a filter reads the value as text)'."\n".'        return $this->sql("SELECT * FROM table|ratings WHERE name = \'{$value}\'");',
        'Number' => '        $length = 191;'."\n".'        return $this->sql("CREATE TABLE %i (name varchar({$length}))", $this->tablename);',
        'PlaceholderList' => '        $in = implode(\',\', array_fill(0, count($ids), \'%d\'));'."\n".'        return $this->sql("SELECT * FROM table|ratings WHERE ID IN ({$in})", ...$ids);',
        'Placeholders' => '        return $this->sql(\'SELECT * FROM %i WHERE name = %s\', $this->tablename, $value);',
    ]);

    expect($errors)->toBe([
        'Built.php' => ['siteReviews.querySql'],
        'Property.php' => ['siteReviews.querySql'],
        'Variable.php' => ['siteReviews.querySql'],
    ]);
});
