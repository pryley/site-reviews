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
        $preset = $args['attrs']['module']['decoration']['stylePreset']['desktop']['value'] ?? '0';
        $ratingColor = $args['attrs']['module']['decoration']['styleRatingColor']['desktop']['value'] ?? '';
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
                                        $maxWidth = $args['attrValue']['maxWidth'] ?? '48ch';
                                        return "--glsr-max-w:{$maxWidth};";
                                    },
                                ],
                            ],
                            [
                                'componentName' => 'divi/common',
                                'props' => [
                                    'attr' => $attrs['module']['decoration']['styleRatingColor'] ?? [],
                                    'property' => '--glsr-summary-star-bg',
                                ],
                            ],
                            [
                                'componentName' => 'divi/common',
                                'props' => [
                                    'attr' => $attrs['module']['decoration']['styleRatingColor'] ?? [],
                                    'property' => '--glsr-bar-bg',
                                ],
                            ],
                            [
                                'componentName' => 'divi/common',
                                'props' => [
                                    'attr' => $attrs['module']['decoration']['styleBarSize'] ?? [],
                                    'property' => '--glsr-bar-size',
                                ],
                            ],
                            [
                                'componentName' => 'divi/common',
                                'props' => [
                                    'attr' => $attrs['module']['decoration']['styleBarSpacing'] ?? [],
                                    'property' => '--glsr-bar-spacing',
                                ],
                            ],
                            [
                                'componentName' => 'divi/common',
                                'props' => [
                                    'attr' => $attrs['module']['decoration']['styleRatingSize'] ?? [],
                                    'property' => '--glsr-summary-star',
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
