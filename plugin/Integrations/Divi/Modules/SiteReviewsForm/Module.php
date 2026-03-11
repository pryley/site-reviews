<?php

namespace GeminiLabs\SiteReviews\Integrations\Divi\Modules\SiteReviewsForm;

use ET\Builder\FrontEnd\Module\Style;
use ET\Builder\Packages\Module\Options\Text\TextClassnames;
use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Integrations\Divi\Defaults\ModuleClassnamesDefaults;
use GeminiLabs\SiteReviews\Integrations\Divi\Defaults\ModuleStylesDefaults;
use GeminiLabs\SiteReviews\Integrations\Divi\Modules\DiviModule;
use GeminiLabs\SiteReviews\Integrations\Divi\StyleDeclarations;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsFormShortcode;

class Module extends DiviModule
{
    public static function blockName(): string
    {
        return 'glsr-divi/form';
    }

    /**
     * This method is equivalent to "module-classnames.ts".
     */
    public static function module_classnames(array $args): void
    {
        parent::module_classnames($args);
        $args = glsr(ModuleClassnamesDefaults::class)->merge($args);
        if (empty($args['attrs']['shortcode']['advanced']['theme']['desktop']['value'])) {
            $ratingColor = $args['attrs']['design']['decoration']['ratingColor']['desktop']['value']['color'] ?? '';
            $args['classnamesInstance']->add('has-custom-color', !empty($ratingColor));
        }
    }

    /**
     * This method is equivalent to "module-styles.tsx".
     */
    public static function module_styles(array $args): void
    {
        $args = glsr(ModuleStylesDefaults::class)->merge($args);
        $attrs = $args['attrs'];
        $baseSelector = '.et-db #page-container .et_pb_section';
        $elements = $args['elements'];
        $orderClass = $args['orderClass'];
        $settings = $args['settings'];
        Style::add([
            'id' => $args['id'],
            'name' => $args['name'],
            'orderIndex' => $args['orderIndex'],
            'storeInstance' => $args['storeInstance'],
            'styles' => [
                $elements->style([
                    'styleProps' => [
                        'advancedStyles' => [
                            [
                                // Rating Color
                                'componentName' => 'divi/common',
                                'props' => [
                                    'attr' => $attrs['design']['decoration']['ratingColor'] ?? [],
                                    'declarationFunction' => StyleDeclarations::color(['--glsr-form-star-bg']),
                                    'selector' => "{$orderClass}.has-custom-color .glsr-form",
                                ],
                            ],
                        ],
                    ],
                ]),
            ],
        ]);
        Style::add([
            'id' => $args['id'],
            'name' => $args['name'],
            'orderIndex' => $args['orderIndex'],
            'storeInstance' => $args['storeInstance'],
            'styles' => [
                $elements->style([
                    'attrName' => 'module',
                    'styleProps' => [
                        'advancedStyles' => [
                            [
                                'componentName' => 'divi/common',
                                'props' => [
                                    'attr' => $attrs['module']['advanced']['text']['text'] ?? [],
                                    'declarationFunction' => StyleDeclarations::orientation(),
                                    'selector' => implode(',', [
                                        "{$baseSelector} {$orderClass} .glsr-field:not(.glsr-layout-inline) .glsr-field-subgroup > *",
                                        "{$baseSelector} {$orderClass} .glsr-layout-inline .glsr-field-subgroup",
                                        "{$baseSelector} {$orderClass} .glsr-range-options input:checked + label",
                                        "{$baseSelector} {$orderClass} .glsr-range-options:not(:has(input:checked))::after",
                                        "{$baseSelector} {$orderClass} .glsr-star-rating",
                                    ]),
                                ],
                            ],
                            [
                                'componentName' => 'divi/text',
                                'props' => [
                                    'attr' => $attrs['module']['advanced']['text'] ?? [],
                                    'propertySelectors' => [
                                        'textShadow' => [
                                            'desktop' => [
                                                'value' => [
                                                    'text-shadow' => implode(',', [
                                                        "{$baseSelector} {$orderClass} .glsr-field",
                                                        "{$baseSelector} {$orderClass} .glsr-input",
                                                        "{$baseSelector} {$orderClass} .glsr-select",
                                                        "{$baseSelector} {$orderClass} .glsr-textarea",
                                                    ]),
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'defaultPrintedStyleAttrs' => $args['defaultPrintedStyleAttrs']['module']['decoration'] ?? [],
                        'disabledOn' => [
                            'disabledModuleVisibility' => $settings['disabledModuleVisibility'] ?? null,
                        ],
                    ],
                ]),
            ],
        ]);
        Style::add([
            'id' => $args['id'],
            'name' => $args['name'],
            'orderIndex' => $args['orderIndex'],
            'storeInstance' => $args['storeInstance'],
            'styles' => [
                $elements->style([
                    'attrName' => 'button',
                    'styleProps' => [
                        'advancedStyles' => [
                            [
                                // Button Alignment
                                'componentName' => 'divi/common',
                                'props' => [
                                    'attr' => $attrs['button']['decoration']['button'] ?? [],
                                    'declarationFunction' => StyleDeclarations::buttonAlignment(),
                                    'selector' => "{$orderClass} .glsr-button_wrapper",
                                ],
                            ],
                        ],
                    ],
                ]),
            ],
        ]);
        parent::module_styles($args);
    }

    public static function shortcodeInstance(): ShortcodeContract
    {
        static $shortcode;
        if (empty($shortcode)) {
            $shortcode = glsr(SiteReviewsFormShortcode::class);
        }
        return $shortcode;
    }
}
