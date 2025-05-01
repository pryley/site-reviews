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
            $attr[] = "--glsr-form-star-bg: var(--wp--preset--color--{$attributes['styleRatingColor']});";
        } elseif (!empty($attributes['styleRatingColorCustom'])) {
            $attr[] = "--glsr-form-star-bg: {$attributes['styleRatingColorCustom']};";
        }
        if (!empty($attributes['styleStarSize'])) {
            $attr[] = "--glsr-form-star: {$attributes['styleStarSize']};";
        }
        return implode('', $attr);
    }
}
