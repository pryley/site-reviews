<?php

namespace GLSR_Breakdance;

use Breakdance\Elements\Element;
use GeminiLabs\SiteReviews\Helpers\Svg;
use GeminiLabs\SiteReviews\Integrations\Breakdance\ElementControlsTrait;
use GeminiLabs\SiteReviews\Integrations\Breakdance\ElementTrait;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewShortcode;

class SiteReview extends Element
{
    use ElementTrait;
    use ElementControlsTrait;

    public static function bdShortcodeClass(): string
    {
        return SiteReviewShortcode::class;
    }

    /**
     * @return string
     */
    public static function uiIcon()
    {
        return Svg::get('assets/images/icons/breakdance/icon-review.svg');
    }

    protected static function controlsForDesign(): array
    {
        return [ // order is intentional
            'style_text_align' => [
                'label' => esc_html_x('Text Align', 'admin-text', 'site-reviews'),
                'options' => [
                    'items' => [
                        [
                            'icon' => 'AlignLeftIcon',
                            'text' => 'Left',
                            'value' => 'left',
                        ],
                        [
                            'icon' => 'AlignCenterIcon',
                            'text' => 'Center',
                            'value' => 'center',
                        ],
                        [
                            'icon' => 'AlignRightIcon',
                            'text' => 'Right',
                            'value' => 'right',
                        ],
                    ],
                    'layout' => 'inline',
                    'type' => 'button_bar',
                ],
            ],
            'style_rating_color' => [
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
            'design.general.style_rating_color' => null,
            'design.general.style_text_align' => 'left',
        ];
    }
}
