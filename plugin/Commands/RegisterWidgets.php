<?php

namespace GeminiLabs\SiteReviews\Commands;

class RegisterWidgets
{
	public $widgets;

	public function __construct( $input )
	{
		$this->widgets = $input;
	}
}
