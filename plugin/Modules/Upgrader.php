<?php

namespace GeminiLabs\SiteReviews\Modules;

use DirectoryIterator;
use GeminiLabs\SiteReviews\Controllers\AdminController;
use GeminiLabs\SiteReviews\Database\CountsManager;
use GeminiLabs\SiteReviews\Database\OptionManager;
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
			$version = str_replace( 'Upgrade_', '', $className );
			if( version_compare( glsr()->version, $version, '<' ))return;
			glsr( 'Modules\\Upgrader\\'.$className );
		});
		$this->updateVersion();
	}

	/**
	 * @return void
	 */
	public function updateVersion()
	{
		$currentVersion = glsr( OptionManager::class )->get( 'version' );
		if( version_compare( $currentVersion, glsr()->version, '<' )) {
			glsr( OptionManager::class )->set( 'version', glsr()->version );
		}
		if( $currentVersion != glsr()->version ) {
			glsr( OptionManager::class )->set( 'version_upgraded_from', $currentVersion );
		}
	}
}
