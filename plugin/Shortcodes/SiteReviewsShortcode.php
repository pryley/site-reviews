<?php

namespace GeminiLabs\SiteReviews\Shortcodes;

use GeminiLabs\SiteReviews\Shortcodes\Shortcode;

class SiteReviewsShortcode extends Shortcode
{
	protected $hiddenKeys = [
		'assigned_to', 'author', 'avatar', 'content', 'date', 'rating', 'response', 'title',
	];
}
