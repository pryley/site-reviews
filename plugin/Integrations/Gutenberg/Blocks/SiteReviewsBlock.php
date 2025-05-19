<?php

namespace GeminiLabs\SiteReviews\Integrations\Gutenberg\Blocks;

use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode;

class SiteReviewsBlock extends Block
{
    public static function shortcodeClass(): string
    {
        return SiteReviewsShortcode::class;
    }

    protected function blockClassAttr(array $attributes): string
    {
        $attr = [];
        if (!empty($attributes['styleRatingColor']) || !empty($attributes['styleRatingColorCustom'])) {
            $attr[] = 'has-custom-color';
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
        if (!empty($attributes['styleReviewSpacing']['top'])) {
            $attr[] = "--glsr-review-row-gap: {$attributes['styleReviewSpacing']['top']};";
        }
        return implode('', $attr);
    }
}
