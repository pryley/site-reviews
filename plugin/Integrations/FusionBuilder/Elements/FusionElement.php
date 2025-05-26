<?php

namespace GeminiLabs\SiteReviews\Integrations\FusionBuilder\Elements;

use GeminiLabs\SiteReviews\Integrations\FusionBuilder\Transformer;
use GeminiLabs\SiteReviews\Integrations\IntegrationShortcode;

abstract class FusionElement extends \Fusion_Element
{
    use IntegrationShortcode;

    public static function elementParameters(array $params): array
    {
        $controls = array_merge($params, static::styleConfig());
        $groups = [ // order is intentional
            'design' => esc_html_x('Design', 'admin-text', 'site-reviews'),
            'general' => esc_html_x('General', 'admin-text', 'site-reviews'),
        ];
        $options = [];
        foreach ($controls as $name => $args) {
            $transform = new Transformer($name, $args);
            $control = $transform->control();
            if ('select' === $control['type'] && empty($control['value'])) {
                continue;
            }
            $control['group'] = $groups[$control['group']] ?? ucfirst($control['group']);
            $options[$name] = $control;
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
        $parameters = static::elementParameters($instance->settings());
        $parameters = glsr()->filterArray("fusion-builder/controls/{$instance->tag}", $parameters);
        fusion_builder_map(fusion_builder_frontend_data(static::class, [
            'icon' => static::shortcodeIcon(),
            'name' => $instance->name,
            'params' => $parameters,
            'shortcode' => $instance->tag,
        ]));
    }

    abstract protected static function shortcodeIcon(): string;

    protected static function styleConfig(): array
    {
        return [];
    }
}
