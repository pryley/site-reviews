<?php

namespace GeminiLabs\SiteReviews\Commands;

class RegisterTinymcePopups
{
	public $popups;

	public function __construct( $input )
	{
		$this->popups = $input;
	}
}
