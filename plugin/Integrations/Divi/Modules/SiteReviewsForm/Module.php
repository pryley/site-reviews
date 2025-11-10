<?php

namespace GeminiLabs\SiteReviews\Integrations\Divi\Modules\SiteReviewsForm;

use ET\Builder\FrontEnd\Module\Style;
use ET\Builder\Packages\StyleLibrary\Utils\StyleDeclarations;
use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Integrations\Divi\Defaults\ModuleClassnamesDefaults;
use GeminiLabs\SiteReviews\Integrations\Divi\Defaults\ModuleStylesDefaults;
use GeminiLabs\SiteReviews\Integrations\Divi\Modules\DiviModule;
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
        $args = glsr(ModuleClassnamesDefaults::class)->merge($args);
        $ratingColor = $args['attrs']['design']['decoration']['ratingColor']['desktop']['value'] ?? '';
        $args['classnamesInstance']->add('has-custom-color', !empty($ratingColor));
        parent::module_classnames($args);
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
                    'attrName' => 'module',
                    'styleProps' => [
                        'advancedStyles' => [
                            [
                                'componentName' => 'divi/common',
                                'props' => [
                                    'attr' => $attrs['module']['advanced']['text']['text'] ?? [],
                                    'declarationFunction' => static::orientationStyleDeclaration(),
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
                        'border' => [
                            'propertySelectors' => [
                                'desktop' => [
                                    'value' => [
                                        'border-radius' => implode(',', [
                                            "{$baseSelector} {$orderClass} .glsr-form .glsr-dropzone",
                                            "{$baseSelector} {$orderClass} .glsr-form .glsr-input",
                                            "{$baseSelector} {$orderClass} .glsr-form .glsr-input-checkbox",
                                            "{$baseSelector} {$orderClass} .glsr-form .glsr-select",
                                            "{$baseSelector} {$orderClass} .glsr-form .glsr-textarea",
                                        ]),
                                        'border-style' => implode(',', [
                                            "{$baseSelector} {$orderClass} .glsr-form .glsr-dropzone",
                                            "{$baseSelector} {$orderClass} .glsr-form .glsr-input",
                                            "{$baseSelector} {$orderClass} .glsr-form .glsr-input-checkbox",
                                            "{$baseSelector} {$orderClass} .glsr-form .glsr-input-radio",
                                            "{$baseSelector} {$orderClass} .glsr-form .glsr-input-range",
                                            "{$baseSelector} {$orderClass} .glsr-form .glsr-select",
                                            "{$baseSelector} {$orderClass} .glsr-form .glsr-textarea",
                                            "{$baseSelector} {$orderClass} .glsr-form .glsr-toggle-track::before",
                                        ]),
                                    ],
                                ],
                            ],
                        ],
                        'boxShadow' => [
                            'selector' => implode(',', [
                                "{$baseSelector} {$orderClass} .glsr-form .glsr-dropzone",
                                "{$baseSelector} {$orderClass} .glsr-form .glsr-input",
                                "{$baseSelector} {$orderClass} .glsr-form .glsr-input-checkbox",
                                "{$baseSelector} {$orderClass} .glsr-form .glsr-input-radio",
                                "{$baseSelector} {$orderClass} .glsr-form .glsr-input-range",
                                "{$baseSelector} {$orderClass} .glsr-form .glsr-select",
                                "{$baseSelector} {$orderClass} .glsr-form .glsr-textarea",
                                "{$baseSelector} {$orderClass} .glsr-form .glsr-toggle-track::before",
                            ]),
                        ],
                        'disabledOn' => [
                            'disabledModuleVisibility' => $settings['disabledModuleVisibility'] ?? null,
                        ],
                    ],
                ]),
                $elements->style([
                    'attrName' => 'button',
                ]),
            ],
        ]);
    }

    public static function shortcodeInstance(): ShortcodeContract
    {
        static $shortcode;
        if (empty($shortcode)) {
            $shortcode = glsr(SiteReviewsFormShortcode::class);
        }
        return $shortcode;
    }

    protected static function orientationStyleDeclaration(): callable
    {
        return static function (array $args): string {
            $orientation = $args['attrValue']['orientation'] ?? null;
            $declarations = new StyleDeclarations([
                'important' => true,
                'returnType' => 'string',
            ]);
            if ($orientation) {
                $declarations->add('display', 'flex');
                $declarations->add('justify-content', $orientation);
            }
            return $declarations->value();
        };
    }
}
