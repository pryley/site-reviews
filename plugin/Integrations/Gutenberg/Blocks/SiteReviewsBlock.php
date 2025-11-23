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
        if (!empty($attributes['styleAlign'])) {
            $classes[] = "items-justified-{$attributes['styleAlign']}";
        }
        return $classes;
    }
}
