<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class SiteReviewsSummaryDefaults extends Defaults
{
	/**
	 * @return array
	 * @todo provide backwards compatibility with deprecated 'show_if_empty' option
	 */
	public function defaults()
	{
		return [
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
