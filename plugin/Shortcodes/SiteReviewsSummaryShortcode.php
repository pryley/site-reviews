<?php

namespace GeminiLabs\SiteReviews\Shortcodes;

use GeminiLabs\SiteReviews\Shortcodes\Shortcode;

class SiteReviewsSummaryShortcode extends Shortcode
{
	protected function hideOptions() {
		return [
			'rating' => __( 'Hide the rating', 'site-reviews' ),
			'stars' => __( 'Hide the stars', 'site-reviews' ),
			'summary' => __( 'Hide the summary', 'site-reviews' ),
			'bars' => __( 'Hide the percentage bars', 'site-reviews' ),
			'if_empty' => __( 'Hide if no reviews are found', 'site-reviews' ),
		];
	}
}
