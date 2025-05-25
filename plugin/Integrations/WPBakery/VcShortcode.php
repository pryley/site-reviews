<?php

namespace GeminiLabs\SiteReviews\Integrations\WPBakery;

use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Integrations\WPBakery\Defaults\ControlDefaults;

abstract class VcShortcode extends \WPBakeryShortCode
{
    /**
     * Override the "vc_map" data with the
     * "vc_element_settings_filter" ($settings, $shortcode) filter hook.
     */
    public static function vcRegister(array $args = []): void
    {
        vc_map(wp_parse_args($args, [
            'base' => static::vcShortcode()->tag,
            'category' => glsr()->name,
            'description' => static::vcShortcode()->description,
            'icon' => static::vcShortcodeIcon(),
            'name' => static::vcShortcode()->name,
            'params' => static::vcShortcodeSettings(),
            'php_class_name' => static::class,
            'show_settings_on_create' => false,
        ]));
    }

    public static function vcShortcode(): ShortcodeContract
    {
        return glsr(static::vcShortcodeClass());
    }

    abstract public static function vcShortcodeClass(): string;

    abstract public static function vcShortcodeIcon(): string;

    /**
     * Override attributes for a setting with the 
     * "vc_mapper_attribute" ($setting, $shortcode) filter hook.
     */
    public static function vcShortcodeSettings(): array
    {
        $groups = [
            'general' => [
                'controls' => [],
                'title' => _x('General', 'admin-text', 'site-reviews'),
            ],
            'design' => [
                'controls' => [],
                'title' => _x('Design', 'admin-text', 'site-reviews'),
            ],
            'advanced' => [
                'controls' => [],
                'title' => _x('Advanced', 'admin-text', 'site-reviews'),
            ],
        ];
        $config = array_merge(static::vcSettingsConfig(), static::vcStyleConfig());
        foreach ($config as $name => $args) {
            $control = static::vcTransformControl($name, $args);
            if ('dropdown' === $control['type'] && empty($control['value'])) {
                continue;
            }
            $group =  $groups[$control['group']]['title'];
            $groups[$control['group']]['controls'][$name] = wp_parse_args(compact('group'), $control);
        }
        $controls = array_merge(...array_column($groups, 'controls'));
        $controls = array_values(array_filter($controls));
        return $controls;
    }

    protected static function vcSettingsConfig(): array
    {
        return static::vcShortcode()->settings();
    }

    protected static function vcStyleConfig(): array
    {
        return [];
    }

    protected static function vcTransformControl(string $name, array $args): array
    {
        $control = glsr(ControlDefaults::class)->merge(
            wp_parse_args(compact('name'), $args)
        );
        if ('hide' === $name) {
            $control['heading'] = _x('Hide', 'admin-text', 'site-reviews');
        }
        // Handle dropdown type: add placeholder as first option if present
        if ('dropdown' === $control['type']
            && !empty($control['options'])
            && !isset($control['options'][''])
            && !empty($control['placeholder'])) {
            $control['options'] = ['' => $control['placeholder']] + $control['options'];
        }
        // Set value to options for dropdown/checkbox if value is not set
        if (isset($control['options']) && !isset($control['value'])) {
            $control['value'] = $control['options'];
        }
        // Flip value array for dropdown/checkbox if options exist
        if (in_array($control['type'], ['checkbox', 'dropdown']) && !empty($control['options'])) {
            $control['value'] = array_flip($control['value']);
        }
        // Set default value for checkbox if not set
        if ('checkbox' === $control['type'] && !isset($control['value'])) {
            $control['value'] = [
                esc_html_x('Yes', 'admin-text', 'site-reviews') => 'true',
            ];
        }
        unset($control['options']);
        return $control;
    }
}
