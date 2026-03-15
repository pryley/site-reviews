<?php

namespace GeminiLabs\SiteReviews\Integrations\Gutenberg\Blocks;

use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode;

class SiteReviewsBlock extends Block
{
    public static function shortcodeClass(): string
    {
        return SiteReviewsShortcode::class;
    }

    /**
     * @return string[]
     */
    protected function blockClasses(array $attributes): array
    {
        $classes = [];
        if (!empty($attributes['style_align'])) {
            $classes[] = "items-justified-{$attributes['style_align']}";
        }
        if (!empty($attributes['style_rating_color']) || !empty($attributes['style_rating_color_custom'])) {
            $classes[] = 'has-rating-color';
        }
        return $classes;
    }

    protected function blockStyles(array $attributes): array
    {
        return array_filter([
            '--glsr-review-star-bg' => $this->resolveColor($attributes, 'style_rating_color', 'style_rating_color_custom'),
        ]);
    }
}
