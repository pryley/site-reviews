<?php

namespace GLSR_Breakdance;

use Breakdance\Elements\Element;
use GeminiLabs\SiteReviews\Helpers\Svg;
use GeminiLabs\SiteReviews\Integrations\Breakdance\ElementControlsTrait;
use GeminiLabs\SiteReviews\Integrations\Breakdance\ElementTrait;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode;

class SiteReviews extends Element
{
    use ElementTrait;
    use ElementControlsTrait;

    public static function bdShortcodeClass(): string
    {
        return SiteReviewsShortcode::class;
    }

    /**
     * @return string
     */
    public static function uiIcon()
    {
        return Svg::get('assets/images/icons/breakdance/icon-reviews.svg');
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
        ];
    }

    /**
     * Returns an array with dot notation (i.e. design.general.*) keys
     */
    protected static function defaultsForDesign(): array
    {
        return [
            'design.general.rating_color' => null,
        ];
    }
}
