<?php

namespace GeminiLabs\SiteReviews\Integrations\Flatsome\Shortcodes;

use GeminiLabs\SiteReviews\Integrations\Flatsome\Transformer;
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
        $controls = glsr()->filterArray('flatsome/controls', $controls, $this->shortcodeInstance());
        $groups = [ // order is intentional
            'general' => esc_html_x('General', 'admin-text', 'site-reviews'),
            'schema' => esc_html_x('Schema', 'admin-text', 'site-reviews'),
            'display' => esc_html_x('Display', 'admin-text', 'site-reviews'),
            'filters' => esc_html_x('Filters', 'admin-text', 'site-reviews'),
            'hide' => esc_html_x('Hide', 'admin-text', 'site-reviews'),
            'text' => esc_html_x('Text', 'admin-text', 'site-reviews'),
            'advanced' => esc_html_x('Advanced', 'admin-text', 'site-reviews'),
            'design' => esc_html_x('Design', 'admin-text', 'site-reviews'),
        ];
        $groupedcontrols = [];
        foreach ($controls as $name => $args) {
            $transformer = new Transformer($name, $args, $this->shortcodeInstance()->tag);
            $control = $transformer->control();
            if (empty($control)) {
                continue;
            }
            $group = $control['group'];
            $groupHeading = $groups[$group] ?? ucfirst($group);
            if (!array_key_exists($group, $groupedcontrols)) {
                $groupedcontrols[$group] = [
                    'collapsed' => true,
                    'heading' => $groupHeading,
                    'options' => [],
                    'type' => 'group',
                ];
            }
            $groupedcontrols[$group]['options'][$control['name']] = $control;
        }
        return array_combine( // we need to prefix the group names in order to target them with CSS
            array_map(fn ($k) => glsr()->prefix.$k, array_keys($groupedcontrols)),
            $groupedcontrols
        );
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
            $shortcode = $this->shortcodeInstance()->tag;
            add_ux_builder_shortcode($shortcode, [
                'category' => glsr()->name,
                'name' => $this->shortcodeInstance()->name,
                'options' => $this->options(),
                'scripts' => $this->scripts(),
                'styles' => $this->styles(),
                'thumbnail' => $this->icon(),
                'wrap' => false,
            ]);
        });
    }

    /**
     * Disabled visibility for now because Flatsome is simply a fancy shortcode
     * wrapper and it doesn't seem possible to intercept the shortcode rendering
     * from Flatsome.
     */
    protected function globalConfig(): array
    {
        return [];
        // $global = [];
        // $visibility = get_template_directory().'/inc/builder/shortcodes/commons/visibility.php';
        // if (file_exists($visibility)) {
        //     $global['visibility'] = require $visibility;
        //     $global['visibility']['group'] = 'advanced';
        // }
        // return $global;
    }

    protected function scripts(): array
    {
        return [];
    }

    protected function settingsConfig(): array
    {
        $hidden = [
            'from' => [ // used to set the "from" attribute
                'type' => 'textfield',
                'default' => 'flatsome',
                'group' => 'hidden',
                'save_when_default' => true,
                'value' => 'flatsome',
            ],
        ];
        return array_merge($hidden, $this->shortcodeInstance()->settings());
    }

    protected function styleConfig(): array
    {
        return [];
    }

    protected function styles(): array
    {
        return [];
    }
}
