<?php

namespace GeminiLabs\SiteReviews\Integrations\Flatsome;

use GeminiLabs\SiteReviews\Integrations\Flatsome\Defaults\ControlDefaults;
use GeminiLabs\SiteReviews\Integrations\IntegrationShortcode;

abstract class FlatsomeShortcode
{
    use IntegrationShortcode;

    abstract public function icon(): string;

    public function options(): array
    {
        $controls = array_merge(
            $this->settingsConfig(),
            $this->styleConfig(),
            $this->globalConfig(),
        );
        $groups = [ // order is intentional
            'design' => esc_html_x('Design', 'admin-text', 'site-reviews'),
            'general' => esc_html_x('General', 'admin-text', 'site-reviews'),
            'schema' => esc_html_x('Schema', 'admin-text', 'site-reviews'),
            'display' => esc_html_x('Display', 'admin-text', 'site-reviews'),
            'filters' => esc_html_x('Filters', 'admin-text', 'site-reviews'),
            'hide' => esc_html_x('Hide', 'admin-text', 'site-reviews'),
            'text' => esc_html_x('Text', 'admin-text', 'site-reviews'),
            'advanced' => esc_html_x('Advanced', 'admin-text', 'site-reviews'),
        ];
        $options = [];
        foreach ($controls as $name => $args) {
            $control = $this->transformControl($name, $args);
            $group = $control['group'] ?? 'general';
            $groupHeading = $groups[$group] ?? ucfirst($group);
            if ('select' === $control['type'] && isset($control['options']) && empty($control['options'])) {
                continue;
            }
            if (!array_key_exists($group, $options)) {
                $options[$group] = [
                    'collapsed' => true,
                    'heading' => $groupHeading,
                    'options' => [],
                    'type' => 'group',
                ];
            }
            $options[$group]['options'][$control['name']] = $control;
        }
        $keyOrder = array_keys($groups); // order results by $groups order
        uksort($options, fn ($a, $b) => array_search($a, $keyOrder) <=> array_search($b, $keyOrder));
        return $options;
    }

    /**
     * Override the "add_ux_builder_shortcode" data with
     * the "ux_builder_shortcode_data_{$tag}" filter hook.
     */
    public function register(): void
    {
        add_action('ux_builder_setup', function () {
            if (!function_exists('add_ux_builder_shortcode')) {
                return;
            }
            add_ux_builder_shortcode($this->shortcodeInstance()->tag, [
                'category' => glsr()->name,
                'name' => $this->shortcodeInstance()->name,
                'options' => $this->options(),
                'thumbnail' => $this->icon(),
                'wrap' => false,
            ]);
        });
    }

    /**
     * Disabled visibility for now because Flatsome is simply a fancy shortcode
     * wrapper and it doesn't look possible to intercept the shortcode render
     * as from Flatsome.
     */
    protected function globalConfig(): array
    {
        $global = [];
        // $visibility = get_template_directory().'/inc/builder/shortcodes/commons/visibility.php';
        // if (file_exists($visibility)) {
        //     $global['visibility'] = require $visibility;
        //     $global['visibility']['group'] = 'advanced';
        // }
        return $global;
    }

    protected function settingsConfig(): array
    {
        return $this->shortcodeInstance()->settings();
    }

    protected function styleConfig(): array
    {
        return [];
    }

    protected function transformControl(string $name, array $args): array
    {
        $control = glsr(ControlDefaults::class)->merge(
            wp_parse_args(compact('name'), $args)
        );
        if ('hide' === $name) {
            $control['description'] = esc_html_x('Flatsome does not support multiple checkboxes here so use the dropdown to select fields that you want to hide.', 'admin-text', 'site-reviews');
        }
        return $control;
    }
}
