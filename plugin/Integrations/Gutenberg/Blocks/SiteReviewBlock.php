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
