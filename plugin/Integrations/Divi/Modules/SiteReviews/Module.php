<?php

namespace GeminiLabs\SiteReviews\Integrations\Divi\Modules\SiteReviews;

use ET\Builder\FrontEnd\Module\Style;
use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Integrations\Divi\Defaults\ModuleClassnamesDefaults;
use GeminiLabs\SiteReviews\Integrations\Divi\Defaults\ModuleStylesDefaults;
use GeminiLabs\SiteReviews\Integrations\Divi\Modules\DiviModule;
use GeminiLabs\SiteReviews\Integrations\Divi\StyleDeclarations;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode;

class Module extends DiviModule
{
    public static function blockName(): string
    {
        return 'glsr-divi/reviews';
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
        $elements = $args['elements'];
        $orderClass = $args['orderClass'];
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
                                    'declarationFunction' => StyleDeclarations::color(['--glsr-review-star-bg']),
                                    'selector' => "{$orderClass}.has-custom-color .glsr-reviews",
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
            $shortcode = glsr(SiteReviewsShortcode::class);
        }
        return $shortcode;
    }
}
