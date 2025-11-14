<?php

namespace GLSR_Breakdance;

use Breakdance\Elements\Element;
use GeminiLabs\SiteReviews\Helpers\Svg;
use GeminiLabs\SiteReviews\Integrations\Breakdance\ElementControlsTrait;
use GeminiLabs\SiteReviews\Integrations\Breakdance\ElementTrait;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsFormShortcode;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;

class SiteReviewsForm extends Element
{
    use ElementTrait;
    use ElementControlsTrait;

    public static function bdDependencies(): array
    {
        return [
            [
                'styles' => [
                    '%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%dependencies-files/awesome-form@1/css/form.css',
                ],
            ],
        ];
    }

    public static function bdShortcodeClass(): string
    {
        return SiteReviewsFormShortcode::class;
    }

    /**
     * @return array[]
     */
    public static function designControls__()
    {
        return [
            // c(
            //     'form_design_options',
            //     'Form Design Options',
            //     [
            //         c(
            //             'spacing',
            //             _x('Spacing', 'admin-text', 'site-reviews'),
            //             [
            //                 c(
            //                     'between_fields',
            //                     'Between Fields',
            //                     [],
            //                     [
            //                         'layout' => 'inline',
            //                         'type' => 'unit',
            //                     ],
            //                     true,
            //                     false,
            //                     [],
            //                 ),
            //                 c(
            //                     'after_label',
            //                     'After Label',
            //                     [],
            //                     [
            //                         'layout' => 'inline',
            //                         'type' => 'unit',
            //                     ],
            //                     true,
            //                     false,
            //                     [],
            //                 ),
            //                 c(
            //                     'sub_label',
            //                     'Sub Label',
            //                     [],
            //                     [
            //                         'layout' => 'inline',
            //                         'type' => 'unit',
            //                     ],
            //                     true,
            //                     false,
            //                     [],
            //                 ),
            //             ],
            //             [
            //                 'sectionOptions' => [
            //                     'type' => 'popout',
            //                 ],
            //                 'type' => 'section',
            //             ],
            //             false,
            //             false,
            //             [],
            //         ),
            //         c(
            //             'fields',
            //             'Fields',
            //             [
            //                 c(
            //                     'background',
            //                     'Background',
            //                     [],
            //                     [
            //                         'layout' => 'inline',
            //                         'type' => 'color',
            //                     ],
            //                     false,
            //                     false,
            //                     [],
            //                 ),
            //                 getPresetSection(
            //                     'EssentialElements\\borders',
            //                     'Borders',
            //                     'borders',
            //                     ['type' => 'popout'],
            //                 ),
            //                 c(
            //                     'focused',
            //                     'Focused',
            //                     [
            //                         c(
            //                             'background',
            //                             'Background',
            //                             [],
            //                             [
            //                                 'layout' => 'inline',
            //                                 'type' => 'color',
            //                             ],
            //                             false,
            //                             false,
            //                             [],
            //                         ),
            //                         c(
            //                             'border',
            //                             'Border',
            //                             [],
            //                             [
            //                                 'layout' => 'inline',
            //                                 'type' => 'color',
            //                             ],
            //                             false,
            //                             false,
            //                             [],
            //                         ),
            //                         c(
            //                             'shadow',
            //                             'Shadow',
            //                             [],
            //                             [
            //                                 'layout' => 'vertical',
            //                                 'type' => 'shadow',
            //                             ],
            //                             false,
            //                             false,
            //                             [],
            //                         ),
            //                     ],
            //                     [
            //                         'layout' => 'inline',
            //                         'sectionOptions' => ['type' => 'popout'],
            //                         'type' => 'section',
            //                     ],
            //                     false,
            //                     false,
            //                     [],
            //                 ),
            //                 getPresetSection(
            //                     'EssentialElements\\spacing_padding_all',
            //                     'Padding',
            //                     'padding',
            //                     ['type' => 'popout']
            //                 ),
            //                 c(
            //                     'placeholder',
            //                     'Placeholder',
            //                     [],
            //                     [
            //                         'layout' => 'inline',
            //                         'type' => 'color',
            //                     ],
            //                     false,
            //                     false,
            //                     [],
            //                 ),
            //                 c(
            //                     'required',
            //                     'Required',
            //                     [
            //                         c(
            //                             'color',
            //                             'Color',
            //                             [],
            //                             [
            //                                 'layout' => 'inline',
            //                                 'type' => 'color',
            //                             ],
            //                             false,
            //                             false,
            //                             [],
            //                         ),
            //                         c(
            //                             'size',
            //                             'Size',
            //                             [],
            //                             [
            //                                 'layout' => 'inline',
            //                                 'type' => 'unit',
            //                             ],
            //                             true,
            //                             false,
            //                             [],
            //                         ),
            //                         c(
            //                             'nudge_x',
            //                             'Nudge X',
            //                             [],
            //                             [
            //                                 'layout' => 'inline',
            //                                 'rangeOptions' => ['min' => 0, 'max' => 10, 'step' => 1],
            //                                 'type' => 'unit',
            //                             ],
            //                             true,
            //                             false,
            //                             [],
            //                         ),
            //                         c(
            //                             'nudge_y',
            //                             'Nudge Y',
            //                             [],
            //                             [
            //                                 'layout' => 'inline',
            //                                 'rangeOptions' => ['min' => 0, 'max' => 10, 'step' => 1],
            //                                 'type' => 'unit',
            //                             ],
            //                             true,
            //                             false,
            //                             [],
            //                         ),
            //                     ],
            //                     [
            //                         'layout' => 'inline',
            //                         'sectionOptions' => ['type' => 'popout'],
            //                         'type' => 'section',
            //                     ],
            //                     false,
            //                     false,
            //                     [],
            //                 ),
            //                 c(
            //                     'advanced',
            //                     'Advanced',
            //                     [
            //                         c(
            //                             'hide_labels',
            //                             'Hide Labels',
            //                             [],
            //                             [
            //                                 'layout' => 'inline',
            //                                 'type' => 'toggle',
            //                             ],
            //                             false,
            //                             false,
            //                             [],
            //                         ),
            //                         c(
            //                             'radio_checkbox',
            //                             'Radio & Checkbox',
            //                             [
            //                                 c(
            //                                     'size',
            //                                     'Size',
            //                                     [],
            //                                     [
            //                                         'layout' => 'inline',
            //                                         'type' => 'unit',
            //                                     ],
            //                                     true,
            //                                     false,
            //                                     [],
            //                                 ),
            //                                 c(
            //                                     'color',
            //                                     'Color',
            //                                     [],
            //                                     [
            //                                         'layout' => 'inline',
            //                                         'type' => 'color',
            //                                     ],
            //                                     false,
            //                                     false,
            //                                     [],
            //                                 ),
            //                             ],
            //                             [
            //                                 'layout' => 'inline',
            //                                 'sectionOptions' => ['type' => 'popout'],
            //                                 'type' => 'section',
            //                             ],
            //                             false,
            //                             false,
            //                             [],
            //                         ),
            //                     ],
            //                     [
            //                         'layout' => 'inline',
            //                         'sectionOptions' => ['type' => 'popout'],
            //                         'type' => 'section',
            //                     ],
            //                     false,
            //                     false,
            //                     [],
            //                 ),
            //             ],
            //             [
            //                 'sectionOptions' => ['type' => 'popout'],
            //                 'type' => 'section',
            //             ],
            //             false,
            //             false,
            //             [],
            //         ),
            //     ],
            //     [
            //         'type' => 'section',
            //     ],
            //     false,
            //     false,
            //     [],
            // ),
        ];
    }

    /**
     * @return string
     */
    public static function uiIcon()
    {
        return Svg::get('assets/images/icons/breakdance/icon-form.svg');
    }
}
