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

class Select extends Base
{
	protected $element = 'select';

	/**
	 * @return string
	 */
	public function render( array $defaults = [] )
	{
		$defaults = wp_parse_args( $defaults, [
			'type' => 'select',
		]);
		return sprintf( '<select %s>%s</select>%s',
			$this->implodeAttributes( $defaults ),
			$this->implodeOptions( 'select_option' ),
			$this->generateDescription()
		);
	}
}
