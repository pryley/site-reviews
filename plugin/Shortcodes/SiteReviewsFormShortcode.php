<?php

namespace GeminiLabs\SiteReviews\Shortcodes;

use GeminiLabs\SiteReviews\Shortcodes\Shortcode;

class SiteReviewsFormShortcode extends Shortcode
{
	const HIDDEN_KEYS = [
		'content', 'email', 'name', 'rating', 'terms', 'title',
	];

	/**
	 * @return array
	 */
	protected function sanitize( array $args )
	{
		if( empty( $args['id'] )) {
			$args['id'] = substr( md5( serialize( $args )), 0, 8 );
		}
		return $args;
	}
}
