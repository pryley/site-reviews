<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2017, Paul Ryley
 * @license   GPLv3
 * @since     2.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Commands;

class RegisterShortcodeButtons
{
	public $shortcodes;

	public function __construct( $input )
	{
		$this->shortcodes = $input;
	}
}
