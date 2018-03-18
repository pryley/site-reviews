<?php

namespace GeminiLabs\SiteReviews\Shortcodes;

use GeminiLabs\SiteReviews\Shortcodes\Shortcode;

class SiteReviewsSummaryShortcode extends Shortcode
{
	protected $hiddenKeys = [
		'bars', 'if_empty', 'rating', 'stars', 'summary',
	];
}
