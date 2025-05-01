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

    protected function blockClassAttr(array $attributes): string
    {
        $attr = [];
        if (!empty($attributes['styleAlign'])) {
            $attr[] = "items-justified-{$attributes['styleAlign']}";
        }
        if (!empty($attributes['styleRatingColor']) || !empty($attributes['styleRatingColorCustom'])) {
            $attr[] = 'has-custom-rating-color';
        }
        return implode(' ', $attr);
    }

    protected function blockStyleAttr(array $attributes): string
    {
        $attr = [];
        if (!empty($attributes['styleRatingColor'])) {
            $attr[] = "--glsr-bar-bg: var(--wp--preset--color--{$attributes['styleRatingColor']});";
            $attr[] = '--glsr-summary-star-bg: var(--glsr-bar-bg);';
        } elseif (!empty($attributes['styleRatingColorCustom'])) {
            $attr[] = "--glsr-bar-bg: {$attributes['styleRatingColorCustom']};";
            $attr[] = '--glsr-summary-star-bg: var(--glsr-bar-bg);';
        }
        if (!empty($attributes['styleBarSize'])) {
            $attr[] = "--glsr-bar-size: {$attributes['styleBarSize']};";
        }
        if (!empty($attributes['styleBarSpacing'])) {
            $attr[] = "--glsr-bar-spacing: {$attributes['styleBarSpacing']};";
        }
        if (!empty($attributes['styleStarSize'])) {
            $attr[] = "--glsr-summary-star: {$attributes['styleStarSize']};";
        }
        if (!empty($attributes['styleMaxWidth'])) {
            $attr[] = "--glsr-max-w: {$attributes['styleMaxWidth']};";
        } else {
            $attr[] = "--glsr-max-w: none";
        }
        return implode('', $attr);
    }
}
