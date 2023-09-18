<?php

namespace GeminiLabs\SiteReviews\Integrations\Elementor;

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
            if (Str::startsWith($key, 'hide-') && !empty($value)) {
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

    /**
     * @return array
     */
    protected function assigned_posts_options()
    {
        return [ // order is intentional
            'custom' => _x('Specific Post ID', 'admin-text', 'site-reviews'),
            'post_id' => _x('The Current Page', 'admin-text', 'site-reviews'),
            'parent_id' => _x('The Parent Page', 'admin-text', 'site-reviews'),
        ];
    }

    /**
     * @return array
     */
    protected function assigned_terms_options()
    {
        return glsr(Database::class)->terms();
    }

    /**
     * @return array
     */
    protected function assigned_users_options()
    {
        return [ // order is intentional
            'custom' => _x('Specific User ID', 'admin-text', 'site-reviews'),
            'user_id' => _x('The Logged-in user', 'admin-text', 'site-reviews'),
            'author_id' => _x('The Page author', 'admin-text', 'site-reviews'),
            'profile_id' => _x('The Profile user (BuddyPress/Ultimate Member)', 'admin-text', 'site-reviews'),
        ];
    }

    /**
     * @return array
     */
    protected function get_review_types()
    {
        $types = glsr()->retrieveAs('array', 'review_types', []);
        if (count($types) > 2) {
            return [
                'default' => 'local',
                'label' => _x('Limit the Type of Reviews', 'admin-text', 'site-reviews'),
                'label_block' => true,
                'options' => $types,
                'type' => \Elementor\Controls_Manager::SELECT,
            ];
        }
        return [];
    }

    /**
     * @return void
     */
    protected function register_controls()
    {
        $controls = [
            'settings' => [
                'label' => _x('Settings', 'admin-text', 'site-reviews'),
                'options' => $this->settings_basic(),
            ],
            'advanced' => [
                'label' => _x('Advanced', 'admin-text', 'site-reviews'),
                'options' => $this->settings_advanced(),
            ],
        ];
        $controls = glsr()->filterArray('elementor/register/controls', $controls, $this);
        array_walk($controls, function ($control, $key) {
            $options = array_filter($control['options']);
            if (!empty($options)) {
                $this->register_shortcode_options($options, $key, $control['label']);
            }
        });
    }

    /**
     * @return void
     */
    protected function register_shortcode_options($options, $tabKey, $tabLabel)
    {
        $this->start_controls_section($tabKey, [
            'label' => $tabLabel,
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);
        foreach ($options as $key => $settings) {
            $this->add_control($key, $settings);
        }
        $this->end_controls_section();
    }

    /**
     * @return void
     */
    protected function render()
    {
        $shortcode = $this->get_shortcode_instance()->build($this->get_settings_for_display(), 'elementor');
        $shortcode = str_replace('class="glsr-fallback">', 'class="glsr-fallback" style="display:none;">', $shortcode);
        echo $shortcode;
    }

    /**
     * @return array
     */
    protected function settings_advanced()
    {
        return [
            'shortcode_id' => [
                'label_block' => true,
                'label' => _x('Custom ID', 'admin-text', 'site-reviews'),
                'type' => \Elementor\Controls_Manager::TEXT,
            ],
            'shortcode_class' => [
                'description' => _x('Separate multiple classes with spaces.', 'admin-text', 'site-reviews'),
                'label_block' => true,
                'label' => _x('Additional CSS classes', 'admin-text', 'site-reviews'),
                'type' => \Elementor\Controls_Manager::TEXT,
            ],
        ];
    }

    /**
     * @return array
     */
    protected function settings_basic()
    {
        return [];
    }
}
