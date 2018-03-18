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

class Textarea extends Base
{
	protected $element = 'textarea';

	/**
	 * @return string
	 */
	public function render( array $defaults = [] )
	{
		$defaults = wp_parse_args( $defaults, [
			'class' => 'large-text',
			'rows'  => 3,
			'type'  => 'textarea',
		]);

		return sprintf( '<textarea %s>%s</textarea>%s',
			$this->implodeAttributes( $defaults ),
			$this->args['value'],
			$this->generateDescription()
		);
	}
}
