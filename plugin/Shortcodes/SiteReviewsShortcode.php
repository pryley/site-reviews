<?php

namespace GeminiLabs\SiteReviews\Shortcodes;

use GeminiLabs\SiteReviews\Shortcodes\Shortcode;

class SiteReviewsShortcode extends Shortcode
{
	const HIDDEN_KEYS = [
		'assigned_to', 'author', 'avatar', 'content', 'date', 'rating', 'response', 'title',
	];
}
