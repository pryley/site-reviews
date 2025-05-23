<?php

namespace GeminiLabs\SiteReviews\Integrations\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Integrations\Elementor\Defaults\ControlDefaults;
use GeminiLabs\SiteReviews\Integrations\IntegrationShortcode;

abstract class ElementorWidget extends Widget_Base
{
    use IntegrationShortcode;

    protected bool $hide_if_all_fields_hidden = true;

    public function get_categories(): array
    {
        return [glsr()->id];
    }

    public function get_icon(): string
    {
        return 'eicon-star-o';
    }

    public function get_name(): string
    {
        return $this->shortcodeInstance()->tag;
    }

    /**
     * Elementor throws a JS error when removing a widget from the page if it
     * has a control with "id" as the name. To fix this, we transformed "id"
     * to "shortcode_id" and "class" to "shortcode_class" (just in case).
     * 
     * When the widget is displayed, we need to convert the setting keys back
     * to their original names.
     * 
     * @param string $settingKey
     *
     * @return mixed
     */
    public function get_settings_for_display($settingKey = null)
    {
        $settings = parent::get_settings_for_display();
        $settings['class'] = $settings['shortcode_class']; // because Elementor throws a JS error for these
        $settings['id'] = $settings['shortcode_id']; // because Elementor throws a JS error for these
        $settings = glsr()->filterArray('elementor/display/settings', $settings, $this);
        if ($settingKey) {
            return Arr::get($settings, $settingKey);
        }
        return $settings;
    }

    public function get_title(): string
    {
        return $this->shortcodeInstance()->name;
    }

    protected function controlGroups(): array
    {
        return [
            'general' => [
                'controls' => [],
                'label' => _x('General', 'admin-text', 'site-reviews'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ],
            'advanced' => [
                'controls' => [],
                'label' => _x('Advanced', 'admin-text', 'site-reviews'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ],
            'design' => [
                'controls' => [],
                'label' => _x('Design', 'admin-text', 'site-reviews'),
                'tab' => Controls_Manager::TAB_STYLE,
            ],
        ];
    }

    protected function controlHeadings(): array
    {
        return [
            'display' => esc_html_x('Display', 'admin-text', 'site-reviews'),
            'hide' => esc_html_x('Hide', 'admin-text', 'site-reviews'),
            'schema' => esc_html_x('Schema', 'admin-text', 'site-reviews'),
            'text' => esc_html_x('Text', 'admin-text', 'site-reviews'),
        ];
    }

    protected function controlSections(): array
    {
        $controls = array_merge(
            array_map(fn ($control) => wp_parse_args($control, ['group' => 'general']), $this->settingsConfig()),
            array_map(fn ($control) => wp_parse_args($control, ['group' => 'design']), $this->styleConfig()),
        );
        $groups = $this->controlGroups();
        $headings = $this->controlHeadings();
        foreach ($controls as $key => $control) {
            $group = $control['group'] ?? '';
            $heading = $group;
            if (!array_key_exists($group, $groups)) {
                $group = 'general';
            }
            $control = $this->transformControl($key, $control);
            if (!array_key_exists($heading, $headings)) {
                $groups[$group]['controls'][$control['name']] = $control;
                continue;
            }
            if (!array_key_exists("separator_{$heading}", $groups[$group]['controls'])) {
                $groups[$group]['controls']["separator_{$heading}"] = [
                    'type' => Controls_Manager::HEADING,
                    'label' => $headings[$heading],
                    'separator' => 'before',
                ];
            }
            $groups[$group]['controls'][$control['name']] = $control;
        }
        return $groups;
    }

    protected function register_controls(): void
    {
        $sections = $this->controlSections();
        $sections = glsr()->filterArray('elementor/register/controls', $sections, $this);
        foreach ($sections as $sectionId => $args) {
            $controls = array_filter($args['controls'] ?? []);
            if (empty($controls)) {
                continue;
            }
            unset($args['controls']);
            $this->start_controls_section($sectionId, $args);
            $this->registerControlsForSection($controls);
            $this->end_controls_section();
        }
    }

    protected function registerControlsForSection(array $controls): void
    {
        $groupTypes = Controls_Manager::get_groups_names();
        foreach ($controls as $controlId => $args) {
            if (!isset($args['type'])) {
                continue;
            }
            if (Controls_Manager::SELECT2 === $args['type'] && empty($args['options'])) {
                continue; // skip select controls with empty options
            }
            if (in_array($args['type'], $groupTypes)) {
                $this->add_group_control($args['type'], $args);
                continue;
            }
            if ($args['is_responsive'] ?? false) { // for responsive controls
                $this->add_responsive_control($controlId, $args);
                continue;
            }
            $this->add_control($controlId, $args);
        }
    }

    protected function render(): void
    {
        $args = $this->get_settings_for_display();
        if ($this->hide_if_all_fields_hidden && !$this->shortcodeInstance()->hasVisibleFields($args)) {
            return;
        }
        $html = $this->shortcodeInstance()->build($args, 'elementor');
        $html = str_replace('class="glsr-fallback">', 'class="glsr-fallback" style="display:none;">', $html);
        echo $html;
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
        return glsr(ControlDefaults::class)->merge(
            wp_parse_args(compact('name'), $args)
        );
    }
}
