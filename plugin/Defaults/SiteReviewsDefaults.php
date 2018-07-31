<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class SiteReviewsDefaults extends Defaults
{
	/**
	 * @return array
	 */
	protected function defaults()
	{
		return [
			'assigned_to' => '',
			'category' => '',
			'class' => '',
			'count' => 5,
			'hide' => '',
			'id' => '',
			'offset' => '',
			'pagination' => false,
			'rating' => 1,
			'schema' => false,
			'title' => '',
			'type' => '',
		];
	}
}
