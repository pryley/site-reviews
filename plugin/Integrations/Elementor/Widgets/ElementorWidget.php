<?php

namespace GeminiLabs\SiteReviews\Integrations\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Integrations\Elementor\Defaults\ControlDefaults;
use GeminiLabs\SiteReviews\Integrations\IntegrationShortcode;
use GeminiLabs\SiteReviews\License;

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
        $settings = glsr()->filterArray('elementor/display_settings', $settings, $this);
        if ($settingKey) {
            return Arr::get($settings, $settingKey);
        }
        return $settings;
    }

    public function get_title(): string
    {
        return $this->shortcodeInstance()->name;
    }

    protected function controlGroupsForContent(): array
    {
        return [
            'advanced' => _x('Advanced', 'admin-text', 'site-reviews'),
            'display' => _x('Display', 'admin-text', 'site-reviews'),
            'general' => _x('General', 'admin-text', 'site-reviews'),
            'hide' => _x('Hide', 'admin-text', 'site-reviews'),
            'schema' => _x('Schema', 'admin-text', 'site-reviews'),
            'text' => _x('Text', 'admin-text', 'site-reviews'),
        ];
    }

    protected function controlGroupsForStyle(): array
    {
        return [
            'design' => _x('Design', 'admin-text', 'site-reviews'),
        ];
    }

    protected function controlSections(): array
    {
        $controls = array_merge(
            array_map(
                fn ($control) => wp_parse_args($control, [
                    'group' => 'general',
                    'tab' => Controls_Manager::TAB_CONTENT,
                ]),
                $this->settingsConfig()
            ),
            array_map(
                fn ($control) => wp_parse_args($control, [
                    'group' => 'design',
                    'tab' => Controls_Manager::TAB_STYLE,
                ]),
                $this->styleConfig()
            ),
        );
        $groups = [
            Controls_Manager::TAB_CONTENT => $this->controlGroupsForContent(),
            Controls_Manager::TAB_STYLE => $this->controlGroupsForStyle(),
        ];
        $controls = glsr()->filterArray('elementor/controls', $controls, $this);
        $groupsLabels = glsr()->filterArray('elementor/groups', $groups, $this);
        $sections = [];
        foreach ($controls as $name => $control) {
            $tab = $control['tab'] ?? '';
            $group = $control['group'] ?? '';
            $section = "section_{$group}";
            if (!isset($groupsLabels[$tab][$group])) {
                continue;
            }
            $sections[$section] ??= [
                'controls' => [],
                'label' => $groupsLabels[$tab][$group],
                'tab' => $tab,
            ];
            $transformedControl = $this->transformControl($name, $control);
            $sections[$section]['controls'][$transformedControl['name']] = $transformedControl;
        }
        return $sections;
    }

    protected function get_upsale_data(): array
    {
        $data = [
            'condition' => !glsr(License::class)->isPremium(),
            'description' => esc_html_x('Upgrade to Site Reviews Premium and get a bunch of additional features.', 'admin-text', 'site-reviews'),
            'image' => glsr()->url('assets/images/premium.svg'),
            'image_alt' => esc_attr_x('Upgrade', 'admin-text', 'site-reviews'),
            'upgrade_text' => esc_html_x('Upgrade Now', 'admin-text', 'site-reviews'),
            'upgrade_url' => glsr_premium_url('site-reviews-premium'),
        ];
        return glsr()->filterArray('elementor/upsale_data', $data, $this);
    }

    protected function register_controls(): void
    {
        foreach ($this->controlSections() as $sectionId => $args) {
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
                continue; // controls must have a type
            }
            if (Controls_Manager::SELECT2 === $args['type'] && empty($args['options'])) {
                continue; // skip select controls with empty options
            }
            if (in_array($args['type'], $groupTypes)) {
                $this->add_group_control($args['type'], $args);
                continue; // this is a grouped control
            }
            if ($args['is_responsive'] ?? false) { // for responsive controls
                $this->add_responsive_control($controlId, $args);
                continue; // this is a responsive control
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
        unset($args['tab']); // only section controls should have the tab set
        return glsr(ControlDefaults::class)->merge(
            wp_parse_args(compact('name'), $args)
        );
    }
}
