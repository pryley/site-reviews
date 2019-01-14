<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;
use GeminiLabs\SiteReviews\Helper;

class ValidateReviewDefaults extends Defaults
{
	/**
	 * @return array
	 */
	protected function defaults()
	{
		return [
			'assign_to' => '',
			'category' => '',
			'content' => '',
			'email' => '',
			'form_id' => '',
			'ip_address' => glsr( Helper::class )->getIpAddress(), // required for Akismet and Blacklist validation
			'name' => '',
			'rating' => '0',
			'terms' => '',
			'title' => '',
		];
	}
}
