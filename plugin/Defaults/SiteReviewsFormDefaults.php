<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class SiteReviewsFormDefaults extends Defaults
{
	/**
	 * @return array
	 */
	protected function defaults()
	{
		return [
			'assign_to' => '',
			'category' => '',
			'class' => '',
			'description' => '',
			'excluded' => '',
			'hide' => '',
			'id' => '',
			'title' => '',
		];
	}
}
