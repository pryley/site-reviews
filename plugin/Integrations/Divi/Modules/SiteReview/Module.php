<?php

namespace GeminiLabs\SiteReviews\Integrations\Divi\Modules\SiteReview;

use ET\Builder\FrontEnd\Module\Style;
use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Integrations\Divi\Defaults\ModuleClassnamesDefaults;
use GeminiLabs\SiteReviews\Integrations\Divi\Defaults\ModuleStylesDefaults;
use GeminiLabs\SiteReviews\Integrations\Divi\Modules\DiviModule;
use GeminiLabs\SiteReviews\Integrations\Divi\StyleDeclarations;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewShortcode;

class Module extends DiviModule
{
    public static function blockName(): string
    {
        return 'glsr-divi/review';
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
        $baseSelector = '#page-container';
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
                                    'selector' => "{$baseSelector} {$orderClass}.has-custom-color .glsr-review",
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
            $shortcode = glsr(SiteReviewShortcode::class);
        }
        return $shortcode;
    }
}
