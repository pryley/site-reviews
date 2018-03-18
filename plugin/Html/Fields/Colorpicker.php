<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Html\Fields;

use GeminiLabs\SiteReviews\Html\Fields\Base;

class Colorpicker extends Text
{
	protected $dependencies = ['wp-color-picker'];

	/**
	 * @return string
	 */
	public function render( array $defaults = [] )
	{
		$value = $this->args['value'];

		$hex_color = '(([a-fA-F0-9]){3}){1,2}$';

		// Prepend "#" if it's missing
		if( preg_match( '/^' . $hex_color . '/i', $value )) {
			$this->args['value'] = "#{$value}";
		}
		// Reset value if it's bad
		else if( !preg_match( '/^#' . $hex_color . '/i', $value )) {
			$this->args['value'] = '';
		}

		return parent::render( wp_parse_args( $defaults, [
			'class'       => 'color-picker-hex',
			'maxlength'   => 7,
			'placeholder' => __( 'Hex Value', 'site-reviews' ),
			'attributes'  => [
				'data-colorpicker' => json_encode([
					'palettes' => [
						'#F44336',
						'#E91E63',
						'#9C27B0',
						'#2196F3',
						'#4CAF50',
						'#FFEB3B',
						'#FF9800',
						'#795548',
						'#9E9E9E',
					],
				]),
			],
		]));
	}
}
