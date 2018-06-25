<?php

defined( 'WPINC' ) || die;

require_once( ABSPATH.WPINC.'/class-phpass.php' );

spl_autoload_register( function( $className ) {
	$namespaces = [
		'GeminiLabs\\SiteReviews\\' => __DIR__.'/plugin/',
		'GeminiLabs\\SiteReviews\\Tests\\' => __DIR__.'/tests/',
		'Sepia\\PoParser\\' => __DIR__.'/vendor/qcubed/i18n/src/Sepia/',
		'Sinergi\\BrowserDetector\\' => __DIR__.'/vendor/sinergi/browser-detector/src/',
		'Vectorface\\Whip\\' => __DIR__.'/vendor/vectorface/whip/src/',
	];
	foreach( $namespaces as $prefix => $baseDir ) {
		$len = strlen( $prefix );
		if( strncmp( $prefix, $className, $len ) !== 0 )continue;
		$file = $baseDir.str_replace( '\\', '/', substr( $className, $len )).'.php';
		if( !file_exists( $file ))continue;
		require $file;
		break;
	}
});
