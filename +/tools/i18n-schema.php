<?php

/*
 * Loaded by `make build:i18n` through `wp --require`.
 */

WP_CLI::add_hook('before_invoke:i18n make-pot', static function (): void {
    $schemas = [
        \WP_CLI\I18n\JsonSchemaExtractor::BLOCK_JSON_SOURCE => ABSPATH.'wp-includes/block-i18n.json',
        \WP_CLI\I18n\JsonSchemaExtractor::THEME_JSON_SOURCE => ABSPATH.'wp-includes/theme-i18n.json',
    ];
    $cache = [];
    foreach ($schemas as $url => $file) {
        $json = is_readable($file) ? file_get_contents($file) : false;
        $schema = false === $json ? null : json_decode($json, false);
        if (!is_object($schema)) {
            WP_CLI::error("Cannot read the i18n schema at {$file} (see +/tools/i18n-schema.php).");
        }
        $cache[$url] = $schema;
    }
    try {
        (new \ReflectionProperty(\WP_CLI\I18n\JsonSchemaExtractor::class, 'schema_cache'))->setValue(null, $cache);
    } catch (\ReflectionException $e) {
        WP_CLI::error('wp-cli/i18n-command no longer has JsonSchemaExtractor::$schema_cache; update +/tools/i18n-schema.php.');
    }
});
