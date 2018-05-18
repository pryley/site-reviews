<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Database\OptionManager;
use ReflectionClass;
use ReflectionMethod;

class Upgrader
{
	public function run()
	{
		$routines = (new ReflectionClass( __CLASS__ ))->getMethods( ReflectionMethod::IS_PROTECTED );
		$routines = array_column( $routines, 'name' );
		natsort( $routines );
		array_walk( $routines, function( $routine ) {
			$version = str_replace( strtolower( __CLASS__ ).'_', '', $routine );
			if( version_compare( glsr()->version, $version, '>=' ))return;
			call_user_func( [$this, $routine] );
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

	protected function upgrade_3_0_0()
	{}
}
