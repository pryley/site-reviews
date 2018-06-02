<?php

namespace GeminiLabs\SiteReviews\Contracts;

interface PartialContract
{
	/**
	 * @return void|string
	 */
	public function build( array $args = [] );
}
