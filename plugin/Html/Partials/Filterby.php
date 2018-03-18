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

class Filterby extends Base
{
	/**
	 * Generate a filter dropdown for the admin post_type table
	 *
	 * @return string|null
	 */
	public function render()
	{
		$defaults = [
			'name'   => '',
			'values' => [],
		];

		$args = shortcode_atts( $defaults, $this->args );

		extract( $args );

		if( !$name || empty( $values ))return;

		$options  = '';

		foreach( $values as $value => $title ) {
			$options .= $this->selectOption( $value, $title, filter_input( INPUT_GET, $name ));
		}

		printf( '<select name="%s">%s</select>', $name, $options );
	}
}
