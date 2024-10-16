<?php

namespace GeminiLabs\SiteReviews\Integrations\Elementor;

use Elementor\Controls_Manager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Rating;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsSummaryShortcode;

class ElementorSummaryWidget extends ElementorWidget
{
    /**
     * @return string
     */
    public function get_icon()
    {
        return 'eicon-rating';
    }

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

    protected function hide_if_all_fields_hidden(): bool
    {
        return true;
    }

    protected function settings_advanced(): array
    {
        $settings = parent::settings_advanced();
        $settings = Arr::insertAfter('shortcode_id', $settings, [
            'rating_field' => [
                'description' => _x('Use the Review Forms addon to add custom rating fields.', 'admin-text', 'site-reviews'),
                'label_block' => true,
                'label' => _x('Custom Rating Field Name', 'admin-text', 'site-reviews'),
                'type' => Controls_Manager::TEXT,
            ],
        ]);
        return $settings;
    }

    protected function settings_basic(): array
    {
        $options = [
            'assigned_posts' => [
                'default' => '',
                'label' => _x('Limit Reviews to an Assigned Page', 'admin-text', 'site-reviews'),
                'label_block' => true,
                'options' => $this->assigned_posts_options(),
                'type' => Controls_Manager::SELECT2,
            ],
            'assigned_posts_custom' => [
                'condition' => ['assigned_posts' => 'custom'],
                'description' => _x('Separate values with a comma.', 'admin-text', 'site-reviews'),
                'label_block' => true,
                'placeholder' => _x('Enter the Post IDs', 'admin-text', 'site-reviews'),
                'show_label' => false,
                'type' => Controls_Manager::TEXT,
            ],
            'assigned_terms' => [
                'default' => '',
                'label' => _x('Limit Reviews to an Assigned Category', 'admin-text', 'site-reviews'),
                'label_block' => true,
                'multiple' => true,
                'options' => $this->assigned_terms_options(),
                'type' => Controls_Manager::SELECT2,
            ],
            'assigned_users' => [
                'default' => '',
                'label' => _x('Limit Reviews to an Assigned User', 'admin-text', 'site-reviews'),
                'label_block' => true,
                'options' => $this->assigned_users_options(),
                'type' => Controls_Manager::SELECT2,
            ],
            'assigned_users_custom' => [
                'condition' => ['assigned_users' => 'custom'],
                'description' => _x('Separate values with a comma.', 'admin-text', 'site-reviews'),
                'label_block' => true,
                'placeholder' => _x('Enter the User IDs', 'admin-text', 'site-reviews'),
                'show_label' => false,
                'type' => Controls_Manager::TEXT,
            ],
            'terms' => [
                'default' => '',
                'label' => _x('Limit Reviews to terms', 'admin-text', 'site-reviews'),
                'label_block' => true,
                'options' => [
                    'true' => _x('Terms were accepted', 'admin-text', 'site-reviews'),
                    'false' => _x('Terms were not accepted', 'admin-text', 'site-reviews'),
                ],
                'type' => Controls_Manager::SELECT2,
            ],
            'type' => $this->get_review_types(),
            'rating' => [
                'default' => 0,
                'label' => _x('Minimum Rating', 'admin-text', 'site-reviews'),
                'max' => Cast::toInt(glsr()->constant('MAX_RATING', Rating::class)),
                'min' => Cast::toInt(glsr()->constant('MIN_RATING', Rating::class)),
                'separator' => 'before',
                'type' => Controls_Manager::NUMBER,
            ],
            'schema' => [
                'description' => _x('The schema should only be enabled once per page.', 'admin-text', 'site-reviews'),
                'label' => _x('Enable the schema?', 'admin-text', 'site-reviews'),
                'return_value' => 'true',
                'separator' => 'before',
                'type' => Controls_Manager::SWITCHER,
            ],
        ];
        $hideOptions = $this->get_shortcode_instance()->getHideOptions();
        foreach ($hideOptions as $key => $label) {
            $separator = $key === key(array_slice($hideOptions, 0, 1)) ? 'before' : 'default';
            $options["hide-{$key}"] = [
                'label' => $label,
                'separator' => $separator,
                'return_value' => '1',
                'type' => Controls_Manager::SWITCHER,
            ];
        }
        return $options;
    }

    protected function settings_layout(): array
    {
        return [
            'alignment' => [
                'default' => 'start',
                'is_responsive' => true,
                'label' => esc_html_x('Alignment', 'admin-text', 'site-reviews'),
                'options' => [
                    'start' => [
                        'title' => esc_html_x('Start', 'admin-text', 'site-reviews'),
                        'icon' => 'eicon-flex eicon-align-start-h',
                    ],
                    'center' => [
                        'title' => esc_html_x('Center', 'admin-text', 'site-reviews'),
                        'icon' => 'eicon-flex eicon-align-center-h',
                    ],
                    'end' => [
                        'title' => esc_html_x('End', 'admin-text', 'site-reviews'),
                        'icon' => 'eicon-flex eicon-align-end-h',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .glsr-summary' => 'justify-content: {{VALUE}};',
                    '{{WRAPPER}} .glsr-summary-text' => 'display: flex; justify-content: {{VALUE}};',
                ],
                'type' => Controls_Manager::CHOOSE,
            ],
            'max_width' => [
                'default' => [
                    'unit' => 'px',
                    'size' => 450,
                ],
                'is_responsive' => true,
                'label' => esc_html_x('Max Width', 'admin-text', 'site-reviews'),
                'range' => [
                    '%' => [
                        'min' => 50,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => 50,
                        'max' => 1000,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .glsr-summary' => '--glsr-max-w: {{SIZE}}{{UNIT}};',
                ],
                'size_units' => $this->set_custom_size_unit(['px', '%']),
                'type' => Controls_Manager::SLIDER,
            ],
            'percentage_bar_height' => [
                'default' => [
                    'unit' => 'em',
                    'size' => 1,
                ],
                'is_responsive' => true,
                'label' => esc_html_x('Percentage Bar Height', 'admin-text', 'site-reviews'),
                'range' => [
                    'em' => [
                        'max' => 1.5,
                        'min' => 0.1,
                        'step' => 0.1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .glsr-summary .glsr-bar-background' => '--glsr-text-md: {{SIZE}}{{UNIT}};',
                ],
                'size_units' => ['em'],
                'type' => Controls_Manager::SLIDER,
            ],
            'percentage_bar_spacing' => [
                'default' => [
                    'size' => 1.5,
                    'unit' => 'em',
                ],
                'is_responsive' => true,
                'label' => esc_html_x('Percentage Bar Spacing', 'admin-text', 'site-reviews'),
                'range' => [
                    'em' => [
                        'max' => 2,
                        'min' => 1,
                        'step' => 0.1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .glsr-summary .glsr-bar' => '--glsr-bar-leading: {{SIZE}}{{UNIT}};',
                ],
                'size_units' => ['em'],
                'type' => Controls_Manager::SLIDER,
            ],
        ];
    }

    protected function settings_rating(): array
    {
        return [
            'rating_color' => [
                'global' => [
                    'default' => '',
                ],
                'label' => esc_html_x('Color', 'admin-text', 'site-reviews'),
                'selectors' => [
                    '{{WRAPPER}} .glsr:not([data-theme]) .glsr-bar-background-percent' => '--glsr-bar-bg: {{VALUE}} !important',
                    '{{WRAPPER}} .glsr:not([data-theme]) .glsr-star-empty' => 'background: {{VALUE}} !important;',
                    '{{WRAPPER}} .glsr:not([data-theme]) .glsr-star-full' => 'background: {{VALUE}} !important;',
                    '{{WRAPPER}} .glsr:not([data-theme]) .glsr-star-half' => 'background: {{VALUE}} !important;',
                ],
                'type' => Controls_Manager::COLOR,
            ],
            'rating_size' => [
                'default' => [
                    'unit' => 'em',
                    'size' => 1.5,
                ],
                'is_responsive' => true,
                'label' => esc_html_x('Star Size', 'admin-text', 'site-reviews'),
                'range' => [
                    'em' => [
                        'min' => 0.25,
                        'max' => 2.25,
                        'step' => 0.125,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .glsr:not([data-theme]) .glsr-star' => '--glsr-summary-star: {{SIZE}}{{UNIT}};',
                ],
                'size_units' => $this->set_custom_size_unit(['em']),
                'type' => Controls_Manager::SLIDER,
            ],
        ];
    }
}
