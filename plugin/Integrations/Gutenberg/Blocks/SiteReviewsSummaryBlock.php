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
        return $classes;
    }

    protected function blockStyles(array $attributes): array
    {
        $styles = [];
        if (!empty($attributes['style_align'])) {
            $alignMap = [
                'left' => 'start',
                'right' => 'end',
            ];
            $styles['--glsr-summary-align'] = $alignMap[$attributes['style_align']] ?? 'center';
        }
        return $styles;
    }
}
