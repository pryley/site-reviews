<?php

namespace GeminiLabs\SiteReviews\Integrations\Gutenberg\Blocks;

use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsSummaryShortcode;

class SiteReviewsSummaryBlock extends Block
{
    public static function shortcodeClass(): string
    {
        return SiteReviewsSummaryShortcode::class;
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
        if (!empty($attributes['style_bar_color']) || !empty($attributes['style_bar_color_custom'])) {
            $classes[] = 'has-bar-color';
        }
        if (!empty($attributes['style_rating_color']) || !empty($attributes['style_rating_color_custom'])) {
            $classes[] = 'has-rating-color';
        }
        return $classes;
    }

    protected function blockStyles(array $attributes): array
    {
        return array_filter([
            '--glsr-bar-bg' => $this->resolveColor($attributes, 'style_bar_color', 'style_bar_color_custom'),
            '--glsr-max-w' => ($attributes['style_max_width'] ?? '') ?: 'none',
            '--glsr-summary-align' => $this->resolveAlign($attributes, 'style_align'),
            '--glsr-summary-star-bg' => $this->resolveColor($attributes, 'style_rating_color', 'style_rating_color_custom'),
        ]);
    }
}
