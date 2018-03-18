<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Html\Fields;

use GeminiLabs\SiteReviews\Html\Fields\Text;

class Submit extends Text
{
	/**
	 * @return string
	 */
	public function render( array $defaults = [] )
	{
		$defaults = wp_parse_args( $defaults, [
			'class' => 'button button-primary',
			'type' => 'submit',
		]);
		$value = $this->args['value'];
		unset( $this->args['name'], $this->args['value'] );
		return sprintf( '<button %s><span></span>%s</button>%s',
			$this->implodeAttributes( $defaults ),
			$value,
			(string) $this->recaptcha()
		);
	}

	/**
	 * @return void|string
	 */
	protected function recaptcha()
	{
		$integration = glsr_get_option( 'reviews-form.recaptcha.integration' );
		if( $integration == 'custom' ) {
			return sprintf( '<div class="glsr-recaptcha-holder" data-sitekey="%s" data-badge="%s" data-size="invisible"></div>',
				sanitize_text_field( glsr_get_option( 'reviews-form.recaptcha.key' )),
				sanitize_text_field( glsr_get_option( 'reviews-form.recaptcha.position' ))
			);
		}
		if( $integration == 'invisible-recaptcha' ) {
			ob_start();
			do_action( 'google_invre_render_widget_action' );
			$html = ob_get_clean();
			return sprintf( '<div class="glsr-recaptcha-holder">%s</div>', $html );
		}
	}
}
