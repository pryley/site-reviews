<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Html\Partials;

use GeminiLabs\SiteReviews\Html\Partials\Base;

class Addon extends Base
{
	/**
	 * Generate an add-on element
	 *
	 * @return string
	 */
	public function render()
	{
		$defaults = [
			'name'        => '',
			'title'       => '',
			'description' => '',
			'link'        => '',
		];

		$args = shortcode_atts( $defaults, $this->args );

		extract( $args );

		$addonPoster = $link
			? sprintf( '<a href="%s" class="glsr-addon-screenshot" data-name="%s"></a>', $link, $name )
			: sprintf( '<div class="glsr-addon-screenshot" data-name="%s"></div>', $name );

		$addonLink = $link
			? sprintf( '<a href="%s" class="glsr-addon-link button button-secondary">%s</a>', $link, __( 'More Info', 'site-reviews' ))
			: '';

		return '' .
		'<div class="glsr-addon-wrap"><div class="glsr-addon">' .
			$addonPoster .
			sprintf( '<div class="glsr-addon-description"><p>%s</p></div>', $description ) .
			sprintf( '<h3 class="glsr-addon-name">%s</h3>%s', $title, $addonLink ) .
		'</div></div>';
	}
}
