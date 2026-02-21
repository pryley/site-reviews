<?php

namespace GLSR_Breakdance;

use Breakdance\Elements\Element;
use GeminiLabs\SiteReviews\Helpers\Svg;
use GeminiLabs\SiteReviews\Integrations\Breakdance\ElementControlsTrait;
use GeminiLabs\SiteReviews\Integrations\Breakdance\ElementTrait;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsFormShortcode;

class SiteReviewsForm extends Element
{
    use ElementTrait;
    use ElementControlsTrait;

    public static function bdDependencies(): array
    {
        return [
            [
                'builderCondition' => 'return true;',
                'frontendCondition' => 'return false;',
                'inlineStyles' => [
                    '%%SELECTOR%% a, %%SELECTOR%% button {pointer-events: none}',
                ],
                'title' => 'Prevent pointer events on buttons in the builder',
            ],
            [
                'styles' => [
                    '%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%dependencies-files/awesome-form@1/css/form.css',
                ],
                'title' => 'Load Breakdance form styles',
            ],
        ];
    }

    public static function bdShortcodeClass(): string
    {
        return SiteReviewsFormShortcode::class;
    }

    /**
     * @return string
     */
    public static function uiIcon()
    {
        return Svg::get('assets/images/icons/breakdance/icon-form.svg');
    }

    protected static function controlsForDesign(): array
    {
        return [
            'rating_color' => [
                'label' => esc_html_x('Rating Color', 'admin-text', 'site-reviews'),
                'options' => [
                    'layout' => 'inline',
                    'type' => 'color',
                ],
            ],
        ];
    }
}
