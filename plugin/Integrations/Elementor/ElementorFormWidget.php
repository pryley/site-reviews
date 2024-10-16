<?php

namespace GeminiLabs\SiteReviews\Integrations\Elementor;

use Elementor\Controls_Manager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsFormShortcode;

class ElementorFormWidget extends ElementorWidget
{
    /**
     * @return string
     */
    public function get_icon()
    {
        return 'eicon-form-horizontal';
    }

    /**
     * @return string
     */
    public function get_shortcode()
    {
        return SiteReviewsFormShortcode::class;
    }

    public function get_title()
    {
        return _x('Review Form', 'admin-text', 'site-reviews');
    }

    protected function hide_if_all_fields_hidden(): bool
    {
        return true;
    }

    protected function settings_advanced(): array
    {
        $settings = parent::settings_advanced();
        $settings = Arr::insertAfter('shortcode_id', $settings, [
            'reviews_id' => [
                'description' => _x('Enter the Custom ID of a reviews block, shortcode, or widget where the review should be displayed after submission.', 'admin-text', 'site-reviews'),
                'label_block' => true,
                'label' => _x('Custom Reviews ID', 'admin-text', 'site-reviews'),
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
                'label' => _x('Assign Reviews to a Page', 'admin-text', 'site-reviews'),
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
                'label' => _x('Assign Reviews to a Category', 'admin-text', 'site-reviews'),
                'label_block' => true,
                'multiple' => true,
                'options' => $this->assigned_terms_options(),
                'type' => Controls_Manager::SELECT2,
            ],
            'assigned_users' => [
                'default' => '',
                'label' => _x('Assign Reviews to a User', 'admin-text', 'site-reviews'),
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
            'spacing' => [
                'default' => [
                    'unit' => 'em',
                    'size' => 0.75,
                ],
                'is_responsive' => true,
                'label' => esc_html_x('Field Spacing', 'admin-text', 'site-reviews'),
                'range' => [
                    'em' => [
                        'min' => 0,
                        'max' => 2,
                        'step' => 0.125,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .glsr-review-form' => '--glsr-gap-md: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .elementor-form .elementor-form-fields-wrapper .glsr-field' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
                'size_units' => $this->set_custom_size_unit(['em']),
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
                    '{{WRAPPER}} .glsr:not([data-theme]) .glsr-field:not(.glsr-field-is-invalid) .glsr-star-rating--stars > span' => 'background: {{VALUE}} !important;',
                    '{{WRAPPER}} .glsr:not([data-theme]) .glsr-field-is-invalid .glsr-star-rating--stars > span.gl-active' => 'background: {{VALUE}} !important;',
                ],
                'type' => Controls_Manager::COLOR,
            ],
            'rating_size' => [
                'default' => [
                    'unit' => 'em',
                    'size' => 2,
                ],
                'is_responsive' => true,
                'label' => esc_html_x('Star Size', 'admin-text', 'site-reviews'),
                'range' => [
                    'em' => [
                        'min' => 1,
                        'max' => 3,
                        'step' => 0.125,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} form.glsr-form .glsr-field-rating' => '--glsr-form-star: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .glsr[data-theme] .glsr-field-rating [data-stars]' => 'font-size: initial;',
                    '{{WRAPPER}} .glsr[data-theme] .glsr-field-rating [data-stars] > span' => 'font-size: initial; height: var(--glsr-form-star); width: var(--glsr-form-star);',
                ],
                'size_units' => $this->set_custom_size_unit(['em']),
                'type' => Controls_Manager::SLIDER,
            ],
            'rating_spacing' => [
                'default' => [
                    'unit' => 'px',
                    'size' => 2,
                ],
                'is_responsive' => true,
                'label' => esc_html_x('Star Spacing', 'admin-text', 'site-reviews'),
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 10,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} form.glsr-form .glsr-field-rating span[data-rating]' => 'column-gap: {{SIZE}}{{UNIT}};',
                ],
                'size_units' => ['px'],
                'type' => Controls_Manager::SLIDER,
            ],
        ];
    }
}
