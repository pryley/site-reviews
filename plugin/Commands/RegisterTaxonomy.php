<?php

namespace GeminiLabs\SiteReviews\Commands;

class RegisterTaxonomy
{
	public $args;

	public function __construct( $input )
	{
		$this->args = $input;
	}
}
