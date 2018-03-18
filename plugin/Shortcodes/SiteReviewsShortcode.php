<?php

namespace GeminiLabs\SiteReviews\Shortcodes;

use GeminiLabs\SiteReviews\Shortcodes\Shortcode;

class SiteReviewsShortcode extends Shortcode
{
	protected $hiddenKeys = [
		'author', 'date', 'excerpt', 'rating', 'response', 'title',
	];
}
