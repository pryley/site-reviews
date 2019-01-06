<?php

namespace GeminiLabs\SiteReviews\Shortcodes;

use GeminiLabs\SiteReviews\Shortcodes\Shortcode;

class SiteReviewsShortcode extends Shortcode
{
	protected function hideOptions() {
		return [
			'title' => __( 'Hide the title', 'site-reviews' ),
			'rating' => __( 'Hide the rating', 'site-reviews' ),
			'date' => __( 'Hide the date', 'site-reviews' ),
			'assigned_to' => __( 'Hide the assigned to link (if shown)', 'site-reviews' ),
			'content' => __( 'Hide the content', 'site-reviews' ),
			'avatar' => __( 'Hide the avatar (if shown)', 'site-reviews' ),
			'author' => __( 'Hide the author', 'site-reviews' ),
			'response' => __( 'Hide the response', 'site-reviews' ),
		];
	}
}
