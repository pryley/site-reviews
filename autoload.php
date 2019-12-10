<?php

defined('WPINC') || die;

require_once ABSPATH.WPINC.'/class-phpass.php';

spl_autoload_register(function ($className) {
    $namespaces = [
        'GeminiLabs\\SiteReviews\\' => __DIR__.'/plugin/',
        'GeminiLabs\\SiteReviews\\Tests\\' => __DIR__.'/tests/',
        'GeminiLabs\\Sepia\\PoParser\\' => __DIR__.'/vendors/sepia/po-parser/',
        'GeminiLabs\\Sinergi\\BrowserDetector\\' => __DIR__.'/vendors/sinergi/browser-detector/',
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
