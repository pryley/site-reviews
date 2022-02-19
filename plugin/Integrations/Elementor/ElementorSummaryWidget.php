<?php

namespace GeminiLabs\SiteReviews\Integrations\Elementor;

use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Rating;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsSummaryShortcode;

class ElementorSummaryWidget extends ElementorWidget
{
    /**
     * @return string
     */
    public function get_shortcode()
    {
        return SiteReviewsSummaryShortcode::class;
    }

    public function get_title()
    {
        return _x('Rating Summary', 'admin-text', 'site-reviews');
    }

    protected function settings_basic()
    {
        $options = [
            'assigned_posts' => [
                'default' => '',
                'label' => _x('Limit Reviews to an Assigned Page', 'admin-text', 'site-reviews'),
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
                'label' => _x('Limit Reviews to an Assigned Category', 'admin-text', 'site-reviews'),
                'label_block' => true,
                'multiple' => true,
                'options' => $this->assigned_terms_options(),
                'type' => \Elementor\Controls_Manager::SELECT2,
            ],
            'assigned_users' => [
                'default' => '',
                'label' => _x('Limit Reviews to an Assigned User', 'admin-text', 'site-reviews'),
                'label_block' => true,
                'multiple' => true,
                'options' => $this->assigned_users_options(),
                'type' => \Elementor\Controls_Manager::SELECT2,
            ],
            'terms' => [
                'default' => '',
                'label' => _x('Limit Reviews to terms', 'admin-text', 'site-reviews'),
                'label_block' => true,
                'options' => [
                    'true' => _x('Terms were accepted', 'admin-text', 'site-reviews'),
                    'false' => _x('Terms were not accepted', 'admin-text', 'site-reviews'),
                ],
                'type' => \Elementor\Controls_Manager::SELECT2,
            ],
            'type' => $this->get_review_types(),
            'rating' => [
                'default' => 0,
                'label' => _x('Minimum Rating', 'admin-text', 'site-reviews'),
                'max' => Cast::toInt(glsr()->constant('MAX_RATING', Rating::class)),
                'min' => Cast::toInt(glsr()->constant('MIN_RATING', Rating::class)),
                'separator' => 'before',
                'type' => \Elementor\Controls_Manager::NUMBER,
            ],
            'schema' => [
                'description' => _x('The schema should only be enabled once per page.', 'admin-text', 'site-reviews'),
                'label' => _x('Enable the schema?', 'admin-text', 'site-reviews'),
                'return_value' => 'true',
                'separator' => 'before',
                'type' => \Elementor\Controls_Manager::SWITCHER,
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
