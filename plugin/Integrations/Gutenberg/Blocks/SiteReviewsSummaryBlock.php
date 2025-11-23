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
        if (!empty($attributes['styleAlign'])) {
            $classes[] = "items-justified-{$attributes['styleAlign']}";
        }
        return $classes;
    }

    protected function blockStyles(array $attributes): array
    {
        $styles = [];
        if (!empty($attributes['styleAlign'])) {
            $alignMap = [
                'left' => 'start',
                'right' => 'end',
            ];
            $styles['--glsr-summary-align'] = $alignMap[$attributes['styleAlign']] ?? 'center';
        }
        return $styles;
    }
}
