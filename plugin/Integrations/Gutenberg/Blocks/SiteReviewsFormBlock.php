<?php

namespace GeminiLabs\SiteReviews\Integrations\Gutenberg\Blocks;

use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsFormShortcode;

class SiteReviewsFormBlock extends Block
{
    public function render(array $attributes): string
    {
        if ('edit' !== filter_input(\INPUT_GET, 'context')) {
            return parent::render($attributes);
        }
        // don't use session storage in the block editor
        add_filter('site-reviews/option/forms/session_storage', '__return_false');
        $rendered = parent::render($attributes);
        remove_filter('site-reviews/option/forms/session_storage', '__return_false');
        return $rendered;
    }

    public static function shortcodeClass(): string
    {
        return SiteReviewsFormShortcode::class;
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
            '--glsr-form-star-bg' => $this->resolveColor($attributes, 'style_rating_color', 'style_rating_color_custom'),
        ]);
    }
}
