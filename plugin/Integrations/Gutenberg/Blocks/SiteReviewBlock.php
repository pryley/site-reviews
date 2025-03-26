<?php

namespace GeminiLabs\SiteReviews\Integrations\Gutenberg\Blocks;

use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewShortcode;

class SiteReviewBlock extends Block
{
    public function shortcode(): ShortcodeContract
    {
        return glsr(SiteReviewShortcode::class);
    }
}
