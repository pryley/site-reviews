<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Application;
use ReflectionClass;
use ReflectionMethod;

class Upgrade
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
	}

	protected function upgrade_3_0_0()
	{}
}
