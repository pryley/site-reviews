<?php

namespace GeminiLabs\SiteReviews\Integrations\Gutenberg\Blocks;

use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsFormShortcode;

class SiteReviewsFormBlock extends Block
{
    public function shortcode(): ShortcodeContract
    {
        return glsr(SiteReviewsFormShortcode::class);
    }
}
