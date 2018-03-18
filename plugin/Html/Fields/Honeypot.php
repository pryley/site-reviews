<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Html\Fields;

use GeminiLabs\SiteReviews\Html\Fields\Hidden;

class Honeypot extends Hidden
{
	/**
	 * @return string
	 */
	public function render( array $defaults = [] )
	{
		$this->args['type'] = 'text';
		$this->args['name'] = 'gotcha';
		$this->args['prefix'] = false;
		$this->args['attributes']['style'] = 'display:none!important';
		$this->args['attributes']['tabindex'] = '-1';
		$this->args['attributes']['autocomplete'] = 'off';

		return parent::render( wp_parse_args( $defaults, [
			'class' => 'glsr-input',
		]));
	}
}
