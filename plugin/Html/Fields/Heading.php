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

class Heading extends Base
{
	protected $outside = true;

	/**
	 * @return string
	 */
	public function render( array $defaults = [] )
	{
		isset( $this->args['desc'] ) ?: $this->args['desc'] = '';

		// WP version check

		return sprintf( '<h2 class="title">%s</h2>%s',
			$this->args['value'],
			wpautop( $this->args['desc'] )
		);
	}
}
