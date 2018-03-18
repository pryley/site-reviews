<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class SiteReviewsSummaryDefaults extends Defaults
{
	/**
	 * @return array
	 */
	public function defaults()
	{
		return [
			// 'show_if_empty' => true, // @todo provide backwards compatibility
			'assigned_to' => '',
			'category' => '',
			'class' => '',
			'hide' => '',
			'id' => '',
			'labels' => '',
			'rating' => 1,
			'schema' => false,
			'text' => '',
			'title' => '',
			'type' => '',
		];
	}
}
