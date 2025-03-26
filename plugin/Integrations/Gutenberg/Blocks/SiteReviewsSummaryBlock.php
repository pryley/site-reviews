<?php

namespace GeminiLabs\SiteReviews\Integrations\Gutenberg\Blocks;

use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsSummaryShortcode;

class SiteReviewsSummaryBlock extends Block
{
    public function shortcode(): ShortcodeContract
    {
        return glsr(SiteReviewsSummaryShortcode::class);
    }
}
