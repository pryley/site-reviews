<?php

namespace GeminiLabs\SiteReviews\Integrations\Elementor;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;

abstract class ElementorWidget extends Widget_Base
{
    /**
     * @var \GeminiLabs\SiteReviews\Shortcodes\Shortcode|callable
     */
    private $_shortcode_instance;

    /**
     * @return array
     */
    public function get_categories()
    {
        return [glsr()->id];
    }

    /**
     * @return string
     */
    public function get_icon()
    {
        return 'eicon-star-o';
    }

    /**
     * @return string
     */
    public function get_name()
    {
        return $this->get_shortcode_instance()->shortcode;
    }

    /**
     * @param string $settingKey
     *
     * @return mixed
     */
    public function get_settings_for_display($settingKey = null)
    {
        $settings = parent::get_settings_for_display();
        $settings['class'] = $settings['shortcode_class']; // @compat
        $settings['id'] = $settings['shortcode_id']; // @compat
        if (!empty($settings['assigned_posts_custom'])) {
            $settings['assigned_posts'] = $settings['assigned_posts_custom'];
        }
        $hide = [];
        foreach ($settings as $key => $value) {
            if (str_starts_with($key, 'hide-') && !empty($value)) {
                $hide[] = Str::removePrefix($key, 'hide-');
            }
        }
        $settings['hide'] = array_filter($hide);
        $settings = glsr()->filterArray('elementor/display/settings', $settings, $this);
        if ($settingKey) {
            return Arr::get($settings, $settingKey);
        }
        return $settings;
    }

    /**
     * @return string
     */
    abstract public function get_shortcode();

    /**
     * @return \GeminiLabs\SiteReviews\Shortcodes\Shortcode
     */
    public function get_shortcode_instance()
    {
        if (is_null($this->_shortcode_instance)) { // @phpstan-ignore-line
            $this->_shortcode_instance = glsr($this->get_shortcode());
        }
        return $this->_shortcode_instance;
    }

    protected function assigned_posts_options(): array
    {
        return [ // order is intentional
            'custom' => _x('Specific Post ID', 'admin-text', 'site-reviews'),
            'post_id' => _x('The Current Page', 'admin-text', 'site-reviews'),
            'parent_id' => _x('The Parent Page', 'admin-text', 'site-reviews'),
        ];
    }

    protected function assigned_terms_options(): array
    {
        return glsr(Database::class)->terms();
    }

    protected function assigned_users_options(): array
    {
        return [ // order is intentional
            'custom' => _x('Specific User ID', 'admin-text', 'site-reviews'),
            'user_id' => _x('The Logged-in user', 'admin-text', 'site-reviews'),
            'author_id' => _x('The Page author', 'admin-text', 'site-reviews'),
            'profile_id' => _x('The Profile user (BuddyPress/Ultimate Member)', 'admin-text', 'site-reviews'),
        ];
    }

    protected function hide_if_all_fields_hidden(): bool
    {
        return false;
    }

    protected function get_control_sections(): array
    {
        return [
            'settings' => [
                'controls' => $this->settings_basic(),
                'label' => _x('Settings', 'admin-text', 'site-reviews'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ],
            'advanced' => [
                'controls' => $this->settings_advanced(),
                'label' => _x('Advanced', 'admin-text', 'site-reviews'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ],
            'style_rating' => [
                'controls' => $this->settings_rating(),
                'label' => _x('Rating', 'admin-text', 'site-reviews'),
                'tab' => Controls_Manager::TAB_STYLE,
            ],
            'style_layout' => [
                'controls' => $this->settings_layout(),
                'label' => _x('Layout', 'admin-text', 'site-reviews'),
                'tab' => Controls_Manager::TAB_STYLE,
            ],
        ];
    }

    protected function get_review_types(): array
    {
        $types = glsr()->retrieveAs('array', 'review_types', []);
        if (count($types) > 2) {
            return [
                'default' => 'local',
                'label' => _x('Limit the Type of Reviews', 'admin-text', 'site-reviews'),
                'label_block' => true,
                'options' => $types,
                'type' => Controls_Manager::SELECT,
            ];
        }
        return [];
    }

    /**
     * @return void
     */
    protected function register_controls()
    {
        $sections = $this->get_control_sections();
        $sections = glsr()->filterArray('elementor/register/controls', $sections, $this);
        foreach ($sections as $key => $args) {
            $controls = array_filter($args['controls'] ?? []);
            if (empty($controls)) {
                continue;
            }
            $this->start_controls_section($key, $args);
            $this->register_controls_for_section($controls);
            $this->end_controls_section();
        }
    }

    protected function register_controls_for_section(array $controls): void
    {
        foreach ($controls as $key => $args) {
            if ($args['group_control_type'] ?? false) {
                $args['name'] = $key;
                $this->add_group_control($args['group_control_type'], $args);
                continue;
            }
            if ($args['is_responsive'] ?? false) {
                $this->add_responsive_control($key, $args);
                continue;
            }
            $this->add_control($key, $args);
        }
    }

    /**
     * @return void
     */
    protected function render()
    {
        $args = $this->get_settings_for_display();
        $shortcode = $this->get_shortcode_instance();
        if ($this->hide_if_all_fields_hidden() && !$shortcode->hasVisibleFields($args)) {
            return;
        }
        $html = $shortcode->build($args, 'elementor');
        $html = str_replace('class="glsr-fallback">', 'class="glsr-fallback" style="display:none;">', $html);
        echo $html;
    }

    protected function set_custom_size_unit(array $units): array
    {
        if (version_compare(\ELEMENTOR_VERSION, '3.10.0', '>=')) {
            $units[] = 'custom';
        }
        return $units;
    }

    protected function settings_advanced(): array
    {
        return [
            'shortcode_id' => [
                'description' => esc_html_x('This should be a unique value.', 'admin-text', 'site-reviews'),
                'label_block' => true,
                'label' => esc_html_x('Custom ID', 'admin-text', 'site-reviews'),
                'type' => Controls_Manager::TEXT,
            ],
            'shortcode_class' => [
                'description' => esc_html_x('Separate multiple classes with spaces.', 'admin-text', 'site-reviews'),
                'label_block' => true,
                'label' => esc_html_x('Additional CSS classes', 'admin-text', 'site-reviews'),
                'type' => Controls_Manager::TEXT,
            ],
        ];
    }

    protected function settings_basic(): array
    {
        return [];
    }

    protected function settings_layout(): array
    {
        return [];
    }

    protected function settings_rating(): array
    {
        return [];
    }
}
