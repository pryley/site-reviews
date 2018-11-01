<?php

namespace GeminiLabs\SiteReviews\Modules;

use DirectoryIterator;
use GeminiLabs\SiteReviews\Controllers\AdminController;
use GeminiLabs\SiteReviews\Database\CountsManager;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helper;
use ReflectionClass;
use ReflectionMethod;

class Upgrader
{
	/**
	 * @return void
	 */
	public function run()
	{
		$filenames = [];
		$iterator = new DirectoryIterator( dirname( __FILE__ ).'/Upgrader' );
		foreach( $iterator as $fileinfo ) {
			if( !$fileinfo->isFile() )continue;
			$filenames[] = $fileinfo->getFilename();
		}
		natsort( $filenames );
		array_walk( $filenames, function( $file ) {
			$className = str_replace( '.php', '', $file );
			$version = str_replace( ['Upgrade_', '_'], ['', '.'], $className );
			$versionSuffix = preg_replace( '/[\d.]+(.+)?/', '${1}', glsr()->version ); // allow alpha/beta versions
			if( version_compare( $this->currentVersion(), $version.$versionSuffix, '>=' ))return;
			glsr( 'Modules\\Upgrader\\'.$className );
			glsr_log()->info( 'Completed Upgrade for v'.$version.$versionSuffix );
		});
		$this->updateVersion();
	}

	/**
	 * @return string
	 */
	public function currentVersion()
	{
		return glsr( OptionManager::class )->get( 'version', '0.0.0' );
	}

	/**
	 * @return void
	 */
	public function updateVersion()
	{
		$currentVersion = $this->currentVersion();
		if( $currentVersion !== glsr()->version ) {
			glsr( OptionManager::class )->set( 'version', glsr()->version );
			glsr( OptionManager::class )->set( 'version_upgraded_from', $currentVersion );
		}
	}
}
