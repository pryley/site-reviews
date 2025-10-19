<?php

namespace GeminiLabs\SiteReviews\Integrations\Divi\Modules\SiteReviews;

use ET\Builder\Framework\DependencyManagement\Interfaces\DependencyInterface;
use ET\Builder\FrontEnd\BlockParser\BlockParserStore;
use ET\Builder\FrontEnd\Module\Style;
use ET\Builder\Packages\Module\Layout\Components\ModuleElements\ModuleElements;
use ET\Builder\Packages\Module\Module as DiviModule;
use ET\Builder\Packages\Module\Options\Css\CssStyle;
use ET\Builder\Packages\Module\Options\Element\ElementComponents;
use ET\Builder\Packages\Module\Options\Element\ElementScriptData;
use ET\Builder\Packages\Module\Options\Text\TextClassnames;
use ET\Builder\Packages\ModuleLibrary\ModuleRegistration;
use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Integrations\Divi\Defaults\ModuleClassnamesDefaults;
use GeminiLabs\SiteReviews\Integrations\Divi\Defaults\ModuleScriptDataDefaults;
use GeminiLabs\SiteReviews\Integrations\Divi\Defaults\ModuleStylesDefaults;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode;

class Module implements DependencyInterface
{
    public function load(): void
    {
        add_action('init', function () {
            $modulePath = glsr()->path('assets/divi/modules-json/site_reviews/');
            ModuleRegistration::register_module($modulePath, [
                'render_callback' => [static::class, 'render_callback'],
            ]);
        });
    }

    /**
     * This method is equivalent to "custom-css.ts".
     */
    public static function custom_css(): array
    {
        return \WP_Block_Type_Registry::get_instance()->get_registered('glsr-divi/reviews')->customCssFields;
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
    }

    /**
     * This method is equivalent to "module-script-data.tsx".
     */
    public static function module_script_data(array $args): void
    {
        $args = glsr(ModuleScriptDataDefaults::class)->merge($args);
        $decoration = $args['attrs']['module']['decoration'] ?? [];
        ElementScriptData::set([
            'id' => $args['id'],
            'selector' => $args['selector'],
            'attrs' => array_merge($decoration, [
                'link' => $attrs['module']['advanced']['link'] ?? [],
            ]),
            'storeInstance' => $args['storeInstance'],
        ]);
    }

    /**
     * This method is equivalent to "module-styles.tsx".
     */
    public static function module_styles(array $args): void
    {
        $args = glsr(ModuleStylesDefaults::class)->merge($args);
        $attrs = $args['attrs'];
        $elements = $args['elements'];
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
                        'defaultPrintedStyleAttrs' => $args['defaultPrintedStyleAttrs']['module']['decoration'] ?? [],
                        'disabledOn' => [
                            'disabledModuleVisibility' => $settings['disabledModuleVisibility'] ?? null,
                        ],
                    ],
                ]),
                // CssStyle::style([
                //     'attr' => $attrs['css'] ?? [],
                //     'cssFields' => static::custom_css(),
                //     'selector' => $args['orderClass'],
                // ]),
            ],
        ]);
    }

    /**
     * This method is equivalent to "edit.tsx".
     *
     * @param array          $attrs    block attributes that were saved by Divi Builder
     * @param string         $content  the block's content
     * @param \WP_Block      $block    parsed block object that is being rendered
     * @param ModuleElements $elements an instance of the ModuleElements class
     */
    public static function render_callback(array $attrs, string $content, \WP_Block $block, ModuleElements $elements): string
    {
        $parent = BlockParserStore::get_parent(
            $block->parsed_block['id'],
            $block->parsed_block['storeInstance']
        );
        $attributes = [];
        foreach (($attrs['shortcode']['advanced'] ?? []) as $key => $field) {
            if (!isset($field['desktop']['value'])) {
                continue;
            }
            $value = $field['desktop']['value'];
            if (is_array($value)) {
                $value = array_map(fn ($item) => $item['value'] ?? $item, $value);
            }
            $attributes[$key] = $value;
        }
        return DiviModule::render([
            'attrs' => $attrs,
            'children' => [
                ElementComponents::component([
                    'attrs' => $attrs['module']['decoration'] ?? [],
                    'id' => $block->parsed_block['id'],
                    'orderIndex' => $block->parsed_block['orderIndex'], // FE only
                    'storeInstance' => $block->parsed_block['storeInstance'], // FE only
                ]),
                static::shortcodeInstance()->build($attributes, 'divi', false), // do not wrap html
            ],
            'classnamesFunction' => [static::class, 'module_classnames'],
            'elements' => $elements,
            'id' => $block->parsed_block['id'],
            'moduleCategory' => $block->block_type->category,
            'name' => $block->block_type->name,
            'orderIndex' => $block->parsed_block['orderIndex'], // FE only
            'parentAttrs' => $parent->attrs ?? [],
            'parentId' => $parent->id ?? '',
            'parentName' => $parent->blockName ?? '',
            'scriptDataComponent' => [static::class, 'module_script_data'],
            'storeInstance' => $block->parsed_block['storeInstance'], // FE only
            'stylesComponent' => [static::class, 'module_styles'],
        ]);
    }

    public static function shortcodeClass(): string
    {
        return SiteReviewsShortcode::class;
    }

    public static function shortcodeInstance(): ShortcodeContract
    {
        static $shortcode;
        if (empty($shortcode)) {
            $shortcode = glsr(static::shortcodeClass());
        }
        return $shortcode;
    }
}
