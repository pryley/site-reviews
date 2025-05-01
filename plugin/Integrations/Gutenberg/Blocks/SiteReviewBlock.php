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

    protected function blockClassAttr(array $attributes): string
    {
        $attr = [];
        if (!empty($attributes['styleRatingColor']) || !empty($attributes['styleRatingColorCustom'])) {
            $attr[] = 'has-custom-rating-color';
        }
        return implode(' ', $attr);
    }

    protected function blockStyleAttr(array $attributes): string
    {
        $attr = [];
        if (!empty($attributes['styleRatingColor'])) {
            $attr[] = "--glsr-review-star-bg: var(--wp--preset--color--{$attributes['styleRatingColor']});";
        } elseif (!empty($attributes['styleRatingColorCustom'])) {
            $attr[] = "--glsr-review-star-bg: {$attributes['styleRatingColorCustom']};";
        }
        if (!empty($attributes['styleStarSize'])) {
            $attr[] = "--glsr-review-star: {$attributes['styleStarSize']};";
        }
        return implode('', $attr);
    }
}
