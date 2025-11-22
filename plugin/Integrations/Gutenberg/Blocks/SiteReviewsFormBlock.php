<?php

namespace GeminiLabs\SiteReviews\Integrations\Gutenberg\Blocks;

use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsFormShortcode;

class SiteReviewsFormBlock extends Block
{
    public static function shortcodeClass(): string
    {
        return SiteReviewsFormShortcode::class;
    }
}
