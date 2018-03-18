<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2017, Paul Ryley
 * @license   GPLv3
 * @since     2.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Handlers;

use Exception;
use GeminiLabs\SiteReviews\Commands\RegisterShortcodeButtons as Command;
use ReflectionException;

class RegisterShortcodeButtons
{
	/**
	 * @return void
	 */
	public function handle( Command $command )
	{
		$properties = [];

		foreach( $command->shortcodes as $slug => $args ) {

			$className = glsr_resolve( 'Helper' )->buildClassName( $slug, 'Shortcodes\Buttons' );
			$shortcode = glsr_resolve( $className )->register( $slug, $args );

			$properties[ $slug ] = $shortcode->properties;
		}

		glsr_app()->mceShortcodes = $properties;
	}
}
