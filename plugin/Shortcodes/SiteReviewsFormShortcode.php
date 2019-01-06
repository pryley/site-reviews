<?php

namespace GeminiLabs\SiteReviews\Shortcodes;

use GeminiLabs\SiteReviews\Shortcodes\Shortcode;

class SiteReviewsFormShortcode extends Shortcode
{
	protected function hideOptions() {
		return [
			'rating' => __( 'Hide the rating field', 'site-reviews' ),
			'title' => __( 'Hide the title field', 'site-reviews' ),
			'content' => __( 'Hide the review field', 'site-reviews' ),
			'name' => __( 'Hide the name field', 'site-reviews' ),
			'email' => __( 'Hide the email field', 'site-reviews' ),
			'terms' => __( 'Hide the terms field', 'site-reviews' ),
		];
	}

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
