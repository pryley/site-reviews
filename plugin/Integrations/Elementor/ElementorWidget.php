<?php

namespace GeminiLabs\SiteReviews\Integrations\Elementor;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
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
            if (!array_key_exists($heading, $headings)) {
                $groups[$group]['controls'][$key] = $this->transformControl($key, $control);
                continue;
            }
            if (!array_key_exists("separator_{$heading}", $groups[$group]['controls'])) {
                $groups[$group]['controls']["separator_{$heading}"] = [
                    'type' => Controls_Manager::HEADING,
                    'label' => $headings[$heading],
                    'separator' => 'before',
                ];
            }
            $groups[$group]['controls'][$key] = $this->transformControl($key, $control);
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
        foreach ($controls as $controlId => $args) {
            if (Controls_Manager::SELECT2 === $args['type'] && empty($args['options'])) {
                continue; // skip select controls with empty options
            }
            if ($args['group_control_type'] ?? false) { // for grouped controls like typography
                $this->add_group_control($args['group_control_type'], $args);
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

    protected function transformControl(string $key, array $args): array
    {
        $args['name'] = $key;
        return glsr(ControlDefaults::class)->merge($args);
    }
}
