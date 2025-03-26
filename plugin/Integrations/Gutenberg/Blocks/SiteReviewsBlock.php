<?php

namespace GeminiLabs\SiteReviews\Integrations\Gutenberg\Blocks;

use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode;

class SiteReviewsBlock extends Block
{
    public function shortcode(): ShortcodeContract
    {
        return glsr(SiteReviewsShortcode::class);
    }
}
