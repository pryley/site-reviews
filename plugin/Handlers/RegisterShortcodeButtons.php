<?php

namespace GeminiLabs\SiteReviews\Handlers;

use GeminiLabs\SiteReviews\Commands\RegisterShortcodeButtons as Command;
use GeminiLabs\SiteReviews\Helper;

class RegisterShortcodeButtons
{
	/**
	 * @return void
	 */
	public function handle( Command $command )
	{
		foreach( $command->shortcodes as $slug => $label ) {
			$buttonClass = glsr( Helper::class )->buildClassName( $slug.'-button', 'Shortcodes' );
			if( !class_exists( $buttonClass )) {
				glsr_log()->error( sprintf( 'Class missing (%s)', $buttonClass ));
				continue;
			}
			$shortcode = glsr( $buttonClass )->register( $slug, [
				'label' => $label,
				'title' => $label,
			]);
			glsr()->mceShortcodes[$slug] = $shortcode->properties;
		}
	}
}
