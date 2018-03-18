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

class Hidden extends Text
{
	/**
	 * @return string
	 */
	public function render( array $defaults = [] )
	{
		if( isset( $this->args['label'] )) {
			unset( $this->args['label'] );
		}
		if( isset( $this->args['desc'] )) {
			unset( $this->args['desc'] );
		}
		if( isset( $this->args['id'] )) {
			unset( $this->args['id'] );
		}
		return parent::render( wp_parse_args( $defaults, [
			'class' => '',
		]));
	}
}
