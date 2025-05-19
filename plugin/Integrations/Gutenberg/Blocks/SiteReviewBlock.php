<?php

namespace GeminiLabs\SiteReviews\Integrations\Gutenberg\Blocks;

use GeminiLabs\SiteReviews\Shortcodes\SiteReviewShortcode;

class SiteReviewBlock extends Block
{
    public function render(array $attributes): string
    {
        if ('edit' === filter_input(INPUT_GET, 'context')) {
            if (empty(wp_count_posts(glsr()->post_type)->publish)) {
                return $this->buildEmptyBlock(
                    _x('No reviews found.', 'admin-text', 'site-reviews')
                );
            }
        }
        return parent::render($attributes);
    }

    public static function shortcodeClass(): string
    {
        return SiteReviewShortcode::class;
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
        return implode('', $attr);
    }
}
