<?php

namespace GeminiLabs\SiteReviews\Integrations\Elementor;

use Elementor\Controls_Manager;
use GeminiLabs\SiteReviews\Review;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewShortcode;

class ElementorReviewWidget extends ElementorWidget
{
    /**
     * @return string
     */
    public function get_icon()
    {
        return 'eicon-glsr-review';
    }

    /**
     * @return string
     */
    public function get_shortcode()
    {
        return SiteReviewShortcode::class;
    }

    /**
     * @return string
     */
    public function get_title()
    {
        return _x('Single Review', 'admin-text', 'site-reviews');
    }

    protected function hide_if_all_fields_hidden(): bool
    {
        return true;
    }

    protected function print_content()
    {
        if (Review::isReview($this->get_settings_for_display('post_id'))) {
            parent::print_content();
        }
    }

    protected function settings_basic(): array
    {
        $options = [
            'post_id' => [
                'default' => '',
                'label' => _x('Review Post ID', 'admin-text', 'site-reviews'),
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
                    '{{WRAPPER}} .glsr:not([data-theme]) .glsr-review' => 'text-align: {{VALUE}}; justify-content: {{VALUE}};',
                    '{{WRAPPER}} .glsr:not([data-theme]) .glsr-review .glsr-review-actions' => 'justify-content: {{VALUE}};',
                    '{{WRAPPER}} .glsr:not([data-theme]) .glsr-review .glsr-review-date' => 'flex: inherit;',
                ],
                'type' => Controls_Manager::CHOOSE,
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
                    '{{WRAPPER}} .glsr:not([data-theme]) .glsr-review .glsr-star-empty' => 'background: {{VALUE}} !important;',
                    '{{WRAPPER}} .glsr:not([data-theme]) .glsr-review .glsr-star-full' => 'background: {{VALUE}} !important;',
                    '{{WRAPPER}} .glsr:not([data-theme]) .glsr-review .glsr-star-half' => 'background: {{VALUE}} !important;',
                ],
                'type' => Controls_Manager::COLOR,
            ],
            'rating_size' => [
                'default' => [
                    'unit' => 'em',
                    'size' => 1.25,
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
                    '{{WRAPPER}} .glsr:not([data-theme]) .glsr-review .glsr-star' => '--glsr-review-star: {{SIZE}}{{UNIT}};',
                ],
                'size_units' => $this->set_custom_size_unit(['em']),
                'type' => Controls_Manager::SLIDER,
            ],
        ];
    }
}
