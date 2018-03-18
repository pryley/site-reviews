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

class Checkbox extends Base
{
	protected $element = 'input';

	public function __construct( array $args = [] )
	{
		parent::__construct( $args );

		if( count( $args['options'] ) > 1 ) {
			$this->multi = true;
		}
	}

	/**
	 * @return string
	 */
	public function render( array $defaults = [] )
	{
		$inline = $this->args['inline'] ? ' class="inline"' : '';

		if( $this->multi ) {
			return sprintf( '<ul%s>%s</ul>%s',
				$inline,
				$this->implodeOptions( 'multi_input_checkbox' ),
				$this->generateDescription()
			);
		}

		return sprintf( '%s%s',
			$this->implodeOptions( 'single_input' ),
			$this->generateDescription()
		);
	}
}
