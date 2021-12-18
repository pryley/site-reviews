<?php

namespace GeminiLabs\SiteReviews\Integrations\Elementor;

use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsFormShortcode;

class ElementorFormWidget extends ElementorWidget
{
    /**
     * @return string
     */
    public function get_shortcode()
    {
        return SiteReviewsFormShortcode::class;
    }

    public function get_title()
    {
        return _x('Submit a Review', 'admin-text', 'site-reviews');
    }

    protected function settings_basic()
    {
        $options = [
            'assigned_posts' => [
                'default' => '',
                'label' => _x('Assign Reviews to a Page', 'admin-text', 'site-reviews'),
                'label_block' => true,
                'options' => $this->assigned_posts_options(),
                'type' => \Elementor\Controls_Manager::SELECT2,
            ],
            'assigned_posts_custom' => [
                'condition' => ['assigned_posts' => 'custom'],
                'description' => _x('Separate with commas.', 'admin-text', 'site-reviews'),
                'label_block' => true,
                'placeholder' => _x('Enter the Post IDs', 'admin-text', 'site-reviews'),
                'show_label' => false,
                'type' => \Elementor\Controls_Manager::TEXT,
            ],
            'assigned_terms' => [
                'default' => '',
                'label' => _x('Assign Reviews to a Category', 'admin-text', 'site-reviews'),
                'label_block' => true,
                'multiple' => true,
                'options' => $this->assigned_terms_options(),
                'type' => \Elementor\Controls_Manager::SELECT2,
            ],
            'assigned_users' => [
                'default' => '',
                'label' => _x('Assign Reviews to a User', 'admin-text', 'site-reviews'),
                'label_block' => true,
                'multiple' => true,
                'options' => $this->assigned_users_options(),
                'type' => \Elementor\Controls_Manager::SELECT2,
            ],
        ];
        $hideOptions = $this->get_shortcode_instance()->getHideOptions();
        foreach ($hideOptions as $key => $label) {
            $separator = $key === key(array_slice($hideOptions, 0, 1)) ? 'before' : 'default';
            $options['hide-'.$key] = [
                'label' => $label,
                'separator' => $separator,
                'return_value' => '1',
                'type' => \Elementor\Controls_Manager::SWITCHER,
            ];
        }
        return $options;
    }
}
