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

    protected function blockStyle(array $attributes): string
    {
        $style = [];
        if (!empty($attributes['summary_bar_size'])) {
            $style[] = "--glsr-bar-size: {$attributes['summary_bar_size']};";
        }
        if (!empty($attributes['summary_star_size'])) {
            $style[] = "--glsr-summary-star: {$attributes['summary_star_size']};";
        }
        if (!empty($attributes['summary_max_width'])) {
            $style[] = "--glsr-max-w: {$attributes['summary_max_width']};";
        } else {
            $style[] = "--glsr-max-w: none";
        }
        return implode('', $style);
    }
}
