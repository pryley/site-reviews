<?php

namespace GeminiLabs\SiteReviews\Integrations\FusionBuilder\Elements;

use GeminiLabs\SiteReviews\Integrations\FusionBuilder\Transformer;
use GeminiLabs\SiteReviews\Integrations\IntegrationShortcode;

abstract class FusionElement extends \Fusion_Element
{
    use IntegrationShortcode;

    public static function elementParameters(): array
    {
        $controls = array_merge(static::settingsConfig(), static::styleConfig());
        $groups = [ // order is intentional
            'design' => esc_html_x('Design', 'admin-text', 'site-reviews'),
            'general' => esc_html_x('General', 'admin-text', 'site-reviews'),
            'advanced' => esc_html_x('Advanced', 'admin-text', 'site-reviews'),
        ];
        $options = [];
        foreach ($controls as $name => $args) {
            $transformer = new Transformer($name, $args);
            $control = $transformer->control();
            if ('select' === $control['type'] && empty($control['value'])) {
                continue;
            }
            $control['group'] = $groups[$control['group']] ?? ucfirst($control['group']);
            $options[] = $control;
        }
        return $options;
    }

    public static function registerElement(): void
    {
        if (!function_exists('fusion_builder_map')) {
            return;
        }
        if (!function_exists('fusion_builder_frontend_data')) {
            return;
        }
        $instance = glsr(static::shortcodeClass());
        $parameters = static::elementParameters();
        $parameters = glsr()->filterArray("fusion-builder/controls/{$instance->tag}", $parameters);
        fusion_builder_map(fusion_builder_frontend_data(static::class, [
            'name' => $instance->name,
            'shortcode' => $instance->tag,
            'icon' => static::shortcodeIcon(),
            'params' => $parameters,
            // 'callback' => [
            //     'action' => glsr()->prefix.'fusion_get_query',
            //     'ajax' => true,
            //     'function' => 'fusion_ajax',
            // ],
        ]));
    }

    abstract protected static function shortcodeIcon(): string;

    protected static function settingsConfig(): array
    {
        return glsr(static::shortcodeClass())->settings();
    }

    protected static function styleConfig(): array
    {
        return [];
    }
}
