<?php

namespace GeminiLabs\SiteReviews\Integrations\Divi\Modules\SiteReviewsSummary;

use ET\Builder\FrontEnd\Module\Style;
use ET\Builder\Packages\Module\Options\Text\TextClassnames;
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
        $args['classnamesInstance']->add(
            TextClassnames::text_options_classnames($args['attrs']['module']['advanced']['text'] ?? [])
        );
        $alignSelf = $args['attrs']['module']['decoration']['sizing']['desktop']['value']['alignSelf'] ?? null;
        $alignSelf = 'left' === $alignSelf ? 'start' : ('right' === $alignSelf ? 'end' : $alignSelf);
        $args['classnamesInstance']->add("items-justified-{$alignSelf}", !empty($alignSelf));
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
