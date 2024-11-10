<?php

namespace GeminiLabs\SiteReviews\Integrations\WPBakery;

use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;

abstract class VcShortcode extends \WPBakeryShortCode
{
    /**
     * Override the "vc_map" data with the
     * "vc_element_settings_filter" ($settings, $shortcode) filter hook.
     */
    public static function vcRegister(array $args = []): void
    {
        vc_map(wp_parse_args($args, [
            'base' => static::vcShortcode()->shortcode,
            'category' => glsr()->name,
            'description' => static::vcShortcode()->description,
            'icon' => static::vcShortcodeIcon(),
            'name' => static::vcShortcode()->name,
            'params' => array_values(array_filter(static::vcShortcodeSettings())),
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
        return [];
    }

    public static function vcTypeOptions(): array
    {
        $types = glsr()->retrieveAs('array', 'review_types', []);
        if (2 > count($types)) {
            return [];
        }
        return [
            'type' => 'dropdown',
            'heading' => esc_html_x('Limit Reviews by Type', 'admin-text', 'site-reviews'),
            'param_name' => 'type',
            'std' => 'local',
            'value' => array_flip($types),
        ];
    }
}
