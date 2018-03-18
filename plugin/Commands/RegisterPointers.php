<?php

namespace GeminiLabs\SiteReviews\Commands;

class RegisterPointers
{
	public $pointers;

	public function __construct( $input )
	{
		$this->pointers = $input;
	}
}
