<?php

namespace GeminiLabs\SiteReviews\Integrations\Gutenberg\Blocks;

use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsSummaryShortcode;

class SiteReviewsSummaryBlock extends Block
{
    public function register(): void
    {
        parent::register();
        register_block_style('site-reviews/summary', [
            'name' => 'variant-1',
            'label' => _x('Style 1', 'admin-text', 'site-reviews'),
            'is_default' => true,
        ]);
        register_block_style('site-reviews/summary', [
            'name' => 'variant-2',
            'label' => _x('Style 2', 'admin-text', 'site-reviews'),
        ]);
    }

    public function shortcode(): ShortcodeContract
    {
        return glsr(SiteReviewsSummaryShortcode::class);
    }
}
