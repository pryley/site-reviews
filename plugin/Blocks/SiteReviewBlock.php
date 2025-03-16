<?php

namespace GeminiLabs\SiteReviews\Blocks;

use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewShortcode;

class SiteReviewBlock extends SiteReviewsBlock
{
    public function shortcode(): ShortcodeContract
    {
        return glsr(SiteReviewShortcode::class);
    }
}
