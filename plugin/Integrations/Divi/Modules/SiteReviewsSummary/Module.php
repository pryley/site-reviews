<?php

namespace GeminiLabs\SiteReviews\Integrations\Divi\Modules\SiteReviewsSummary;

use ET\Builder\FrontEnd\Module\Style;
use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Integrations\Divi\Defaults\ModuleClassnamesDefaults;
use GeminiLabs\SiteReviews\Integrations\Divi\Defaults\ModuleStylesDefaults;
use GeminiLabs\SiteReviews\Integrations\Divi\Modules\DiviModule;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsSummaryShortcode;

class Module extends DiviModule
{
    public static function blockName(): string
    {
        return 'glsr-divi/summary';
    }

    /**
     * This method is equivalent to "module-classnames.ts".
     */
    public static function module_classnames(array $args): void
    {
        $args = glsr(ModuleClassnamesDefaults::class)->merge($args);
        $alignSelf = $args['attrs']['module']['decoration']['sizing']['desktop']['value']['alignSelf'] ?? '';
        $alignSelf = ['start' => 'left', 'end' => 'right'][$alignSelf] ?? $alignSelf;
        $preset = $args['attrs']['design']['decoration']['preset']['desktop']['value'] ?? '0';
        $ratingColor = $args['attrs']['design']['decoration']['ratingColor']['desktop']['value'] ?? '';
        $args['classnamesInstance']->add('has-custom-color', !empty($ratingColor));
        $args['classnamesInstance']->add("is-style-{$preset}", !empty($preset));
        $args['classnamesInstance']->add("items-justified-{$alignSelf}", '0' !== $alignSelf);
        parent::module_classnames($args);
    }

    /**
     * This method is equivalent to "module-styles.tsx".
     */
    public static function module_styles(array $args): void
    {
        $args = glsr(ModuleStylesDefaults::class)->merge($args);
        $attrs = $args['attrs'];
        $elements = $args['elements'];
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
                                    'attr' => $attrs['module']['decoration']['sizing'] ?? [],
                                    'selector' => "{$args['orderClass']} .glsr",
                                    'declarationFunction' => function ($args) {
                                        return !empty($args['attrValue']['maxWidth']) ? '--glsr-max-w:none;' : '';
                                    },
                                ],
                            ],
                        ],
                    ],
                ]),
                $elements->style([
                    'attrName' => 'design',
                    'styleProps' => [
                        'advancedStyles' => [
                            [
                                'componentName' => 'divi/common',
                                'props' => [
                                    'attr' => $attrs['design']['decoration']['ratingColor'] ?? [],
                                    'declarationFunction' => function ($args) {
                                        $value = $args['attrValue'] ?? '';
                                        return !empty($value) ? "--glsr-bar-bg:{$value}; --glsr-summary-star-bg:var(--glsr-bar-bg);" : '';
                                    },
                                ],
                            ],
                            [
                                'componentName' => 'divi/common',
                                'props' => [
                                    'attr' => $attrs['design']['decoration']['ratingSize'] ?? [],
                                    'property' => '--glsr-summary-star',
                                ],
                            ],
                            [
                                'componentName' => 'divi/common',
                                'props' => [
                                    'attr' => $attrs['design']['decoration']['barSize'] ?? [],
                                    'property' => '--glsr-bar-size',
                                ],
                            ],
                            [
                                'componentName' => 'divi/common',
                                'props' => [
                                    'attr' => $attrs['design']['decoration']['barSpacing'] ?? [],
                                    'property' => '--glsr-bar-spacing',
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
            $shortcode = glsr(SiteReviewsSummaryShortcode::class);
        }
        return $shortcode;
    }
}
