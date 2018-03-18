<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class CreateReviewDefaults extends Defaults
{
	/**
	 * @return array
	 */
	public function defaults()
	{
		return [
			'assigned_to' => '',
			'author' => '',
			'avatar' => '',
			'content' => '',
			'date' => get_date_from_gmt( gmdate( 'Y-m-d H:i:s' )),
			'email' => '',
			'ip_address' => '',
			'pinned' => false,
			'rating' => '',
			'response' => '',
			'review_id' => md5( time().mt_rand() ),
			'review_type' => 'local',
			'title' => '',
			'url' => '',
		];
	}
}
