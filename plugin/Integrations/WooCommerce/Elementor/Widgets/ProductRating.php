<?php

namespace GeminiLabs\SiteReviews\Integrations\WooCommerce\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use ElementorPro\Modules\Woocommerce\Widgets\Product_Rating;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Modules\Style;

class ProductRating extends Product_Rating
{
    protected function register_controls()
    {
        $this->start_controls_section('section_product_rating_style', [
            'label' => esc_html__('Style', 'elementor-pro'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);
        $this->add_control('wc_style_warning', [
            'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
            'raw' => esc_html__('The style of this widget is often affected by your theme and plugins. If you experience any such issue, try to switch to a basic theme and deactivate related plugins.', 'elementor-pro'),
            'type' => Controls_Manager::RAW_HTML,
        ]);
        $this->add_control('link_color', [
            'label' => esc_html__('Link Color', 'elementor-pro'),
            'selectors' => [
                '.woocommerce {{WRAPPER}} .woocommerce-review-link' => 'color: {{VALUE}}',
            ],
            'type' => Controls_Manager::COLOR,
        ]);
        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'text_typography',
            'selector' => '.woocommerce {{WRAPPER}} .woocommerce-review-link',
        ]);
        $this->add_control('star_size', [
            'default' => [
                'unit' => 'em',
                'size' => 1,
            ],
            'label' => esc_html__('Star Size', 'elementor-pro'),
            'range' => [
                'em' => [
                    'min' => 0,
                    'max' => 4,
                    'step' => 0.1,
                ],
                'px' => [
                    'min' => 0,
                    'max' => 50,
                    'step' => 1,
                ],
            ],
            'selectors' => [
                '.woocommerce {{WRAPPER}} .glsr-star' => 'background-size:{{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}};width:{{SIZE}}{{UNIT}};',
                '.woocommerce {{WRAPPER}} .glsr-rating-level' => 'font-size:{{SIZE}}{{UNIT}};',
            ],
            'size_units' => ['px', 'em'],
            'type' => Controls_Manager::SLIDER,
        ]);
        $this->add_control('space_between', [
            'default' => [
                'unit' => 'em',
                'size' => 0,
            ],
            'label' => esc_html__('Space Between', 'elementor-pro'),
            'range' => [
                'em' => [
                    'min' => 0,
                    'max' => 4,
                    'step' => 0.1,
                ],
                'px' => [
                    'min' => 0,
                    'max' => 50,
                    'step' => 1,
                ],
            ],
            'selectors' => [
                '.woocommerce:not(.rtl) {{WRAPPER}} .glsr-star' => 'margin-right: {{SIZE}}{{UNIT}}',
                '.woocommerce.rtl {{WRAPPER}} .glsr-star ' => 'margin-left: {{SIZE}}{{UNIT}}',
            ],
            'size_units' => ['px', 'em'],
            'type' => Controls_Manager::SLIDER,
        ]);
        $this->add_responsive_control('alignment', [
            'label' => esc_html__('Alignment', 'elementor-pro'),
            'options' => [
                'left' => [
                    'title' => esc_html__('Left', 'elementor-pro'),
                    'icon' => 'eicon-text-align-left',
                ],
                'center' => [
                    'title' => esc_html__('Center', 'elementor-pro'),
                    'icon' => 'eicon-text-align-center',
                ],
                'right' => [
                    'title' => esc_html__('Right', 'elementor-pro'),
                    'icon' => 'eicon-text-align-right',
                ],
                'justify' => [
                    'title' => esc_html__('Justified', 'elementor-pro'),
                    'icon' => 'eicon-text-align-justify',
                ],
            ],
            'prefix_class' => 'elementor-product-rating--align-',
            'type' => Controls_Manager::CHOOSE,
        ]);
        $this->end_controls_section();
    }

    protected function render()
    {
        global $product;
        if ($product = wc_get_product()) {
            glsr(Template::class)->render('templates/woocommerce/rating', [
                'product' => $product,
                'ratings' => glsr_get_ratings(['assigned_posts' => 'post_id']),
                'style' => 'glsr glsr-'.glsr(Style::class)->styleClasses(),
                'theme' => glsr_get_option('integrations.woocommerce.style'),
            ]);
        }
    }
}
