<?php

namespace GeminiLabs\SiteReviews\Integrations\Divi\Modules\SiteReviewsSummary;

use ET\Builder\FrontEnd\Module\Style;
use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Integrations\Divi\Defaults\ModuleClassnamesDefaults;
use GeminiLabs\SiteReviews\Integrations\Divi\Defaults\ModuleStylesDefaults;
use GeminiLabs\SiteReviews\Integrations\Divi\Modules\DiviModule;
use GeminiLabs\SiteReviews\Integrations\Divi\StyleDeclarations;
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
        parent::module_classnames($args);
        $args = glsr(ModuleClassnamesDefaults::class)->merge($args);
        if (empty($args['attrs']['shortcode']['advanced']['theme']['desktop']['value'])) {
            $ratingColor = $args['attrs']['design']['decoration']['ratingColor']['desktop']['value']['color'] ?? '';
            $args['classnamesInstance']->add('has-custom-color', !empty($ratingColor));
        }
        $alignSelf = $args['attrs']['module']['decoration']['sizing']['desktop']['value']['alignSelf'] ?? null;
        if (!empty($alignSelf)) {
            $normalized = str_replace('flex-', '', $alignSelf);
            $mapping = [
                'end' => 'right',
                'start' => 'left',
            ];
            $justified = $mapping[$normalized] ?? $normalized;
            $args['classnamesInstance']->add("items-justified-{$justified}");
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
                    'attrName' => 'module',
                    'styleProps' => [
                        'advancedStyles' => [
                            [
                                'componentName' => 'divi/common',
                                'props' => [
                                    'attr' => $attrs['module']['decoration']['sizing'] ?? [],
                                    'selector' => "{$baseSelector} {$orderClass} .glsr",
                                    'declarationFunction' => function ($args) {
                                        return !empty($args['attrValue']['maxWidth']) ? '--glsr-max-w:none;' : '';
                                    },
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
                    'styleProps' => [
                        'advancedStyles' => [
                            [
                                // Rating Color
                                'componentName' => 'divi/common',
                                'props' => [
                                    'attr' => $attrs['design']['decoration']['ratingColor'] ?? [],
                                    'declarationFunction' => StyleDeclarations::color(['--glsr-summary-star-bg']),
                                    'selector' => "{$baseSelector} {$orderClass}.has-custom-color .glsr-summary",
                                ],
                            ],
                            [
                                // Bar Color
                                'componentName' => 'divi/common',
                                'props' => [
                                    'attr' => $attrs['design']['decoration']['barColor'] ?? [],
                                    'declarationFunction' => StyleDeclarations::color(['--glsr-bar-bg']),
                                    'selector' => "{$baseSelector} {$orderClass} .glsr-summary",
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
