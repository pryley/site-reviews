<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Commands;

class RegisterShortcodes
{
	public $shortcodes;

	public function __construct( $input )
	{
		$this->shortcodes = $input;
	}
}
