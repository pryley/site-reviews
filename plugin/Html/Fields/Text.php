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

class Text extends Base
{
	protected $element = 'input';

	/**
	 * @return string
	 */
	public function render( array $defaults = [] )
	{
		$defaults = wp_parse_args( $defaults, [
			'class' => 'regular-text',
			'type'  => 'text',
		]);

		return sprintf( '<input %s/>%s',
			$this->implodeAttributes( $defaults ),
			$this->generateDescription()
		);
	}
}
