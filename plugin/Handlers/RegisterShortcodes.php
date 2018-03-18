<?php

namespace GeminiLabs\SiteReviews\Handlers;

use GeminiLabs\SiteReviews\Commands\RegisterShortcodes as Command;
use GeminiLabs\SiteReviews\Helper;

class RegisterShortcodes
{
	/**
	 * @return void
	 */
	public function handle( Command $command )
	{
		foreach( $command->shortcodes as $shortcode ) {
			$shortcodeClass = glsr( Helper::class )->buildClassName( $shortcode.'-shortcode', 'Shortcodes' );
			if( !class_exists( $shortcodeClass )) {
				glsr_log()->error( sprintf( 'Class missing (%s)', $shortcodeClass ));
				continue;
			}
			add_shortcode( $shortcode, [glsr( $shortcodeClass ), 'buildShortcode'] );
		}
	}
}
