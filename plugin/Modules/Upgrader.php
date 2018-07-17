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
			$parts = explode( '__', $routine );
			if( version_compare( glsr()->version, end( $parts ), '>=' ))return;
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

	/**
	 * @return void
	 */
	protected function setReviewCounts__3_0_0()
	{
		// 1. calculate the review counts
	}
}
