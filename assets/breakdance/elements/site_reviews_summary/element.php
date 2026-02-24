<?php

namespace GLSR_Breakdance;

use Breakdance\Elements\Element;
use GeminiLabs\SiteReviews\Helpers\Svg;
use GeminiLabs\SiteReviews\Integrations\Breakdance\ElementControlsTrait;
use GeminiLabs\SiteReviews\Integrations\Breakdance\ElementTrait;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsSummaryShortcode;

class SiteReviewsSummary extends Element
{
    use ElementTrait;
    use ElementControlsTrait;

    public static function bdShortcodeClass(): string
    {
        return SiteReviewsSummaryShortcode::class;
    }

    /**
     * @return string
     */
    public static function uiIcon()
    {
        return Svg::get('assets/images/icons/breakdance/icon-summary.svg');
    }

    protected static function controlsForDesign(): array
    {
        return [ // order is intentional
            'rating_color' => [
                'label' => esc_html_x('Rating Color', 'admin-text', 'site-reviews'),
                'options' => [
                    'layout' => 'inline',
                    'type' => 'color',
                ],
            ],
            'bar_color' => [
                'label' => esc_html_x('Bar Color', 'admin-text', 'site-reviews'),
                'options' => [
                    'layout' => 'inline',
                    'type' => 'color',
                ],
            ],
            'max_width' => [
                'label' => esc_html_x('Max Width', 'admin-text', 'site-reviews'),
                'options' => [
                    'layout' => 'inline',
                    'type' => 'unit',
                ],
            ],
        ];
    }

    /**
     * Returns an array with dot notation (i.e. design.general.*) keys
     */
    protected static function defaultsForDesign(): array
    {
        return [
            'design.general.bar_color' => null,
            'design.general.max_width' => [
                'number' => 480,
                'style' => '480px',
                'unit' => 'px',
            ],
            'design.general.rating_color' => null,
        ];
    }
}
