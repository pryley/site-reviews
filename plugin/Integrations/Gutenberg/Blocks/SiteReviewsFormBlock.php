<?php

namespace GeminiLabs\SiteReviews\Integrations\Gutenberg\Blocks;

use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsFormShortcode;

class SiteReviewsFormBlock extends Block
{
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
        return $classes;
    }
}
