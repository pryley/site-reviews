<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;
use GeminiLabs\SiteReviews\Helper;

class CreateReviewDefaults extends Defaults
{
	/**
	 * @return array
	 */
	protected function defaults()
	{
		return [
			'assigned_to' => '',
			'author' => '',
			'avatar' => '',
			'content' => '',
			'custom' => '',
			'date' => get_date_from_gmt( current_time( 'mysql', 1 )),
			'email' => '',
			'ip_address' => glsr( Helper::class )->getIpAddress(),
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
