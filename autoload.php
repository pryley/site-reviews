<?php

defined('ABSPATH') || die;

spl_autoload_register(function ($className) {
    $classMap = [
        'WP_Posts_List_Table' => ABSPATH.'wp-admin/includes/class-wp-posts-list-table.php',
    ];
    if (array_key_exists($className, $classMap) && file_exists($classMap[$className])) {
        require_once $classMap[$className];
    }
    $namespaces = [
        'GeminiLabs\\SiteReviews\\' => __DIR__.'/plugin/',
        'GeminiLabs\\SiteReviews\\Tests\\' => __DIR__.'/tests/',
        'GeminiLabs\\League\\Csv\\' => __DIR__.'/vendors/thephpleague/csv/',
        'GeminiLabs\\Sepia\\PoParser\\' => __DIR__.'/vendors/sepia/po-parser/',
        'GeminiLabs\\Sinergi\\BrowserDetector\\' => __DIR__.'/vendors/sinergi/browser-detector/',
        'GeminiLabs\\Symfony\\Polyfill\\Mbstring\\' => __DIR__.'/vendors/symfony/polyfill-mbstring/',
        'GeminiLabs\\Vectorface\\Whip\\' => __DIR__.'/vendors/vectorface/whip/',
    ];
    foreach ($namespaces as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (0 !== strncmp($prefix, $className, $len)) {
            continue;
        }
        $file = $baseDir.str_replace('\\', '/', substr($className, $len)).'.php';
        if (!file_exists($file)) {
            continue;
        }
        require $file;
        break;
    }
});

if (!function_exists('wp_hash')) {
    require_once ABSPATH.WPINC.'/pluggable.php';
}
if (!function_exists('is_plugin_active_for_network')) {
    require_once ABSPATH.'/wp-admin/includes/plugin.php';
}

/**
 * Provide a partial, native PHP implementation for the Mbstring extension.
 * @see https://github.com/symfony/polyfill-mbstring
 */
if (!extension_loaded('mbstring')) {
    require_once __DIR__.'/vendors/symfony/polyfill-mbstring/bootstrap.php';
}
