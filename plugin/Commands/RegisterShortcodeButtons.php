<?php

namespace GeminiLabs\SiteReviews\Commands;

class RegisterShortcodeButtons
{
	public $shortcodes;

	public function __construct( $input )
	{
		$this->shortcodes = $input;
	}
}
