<?php

namespace GeminiLabs\SiteReviews\Integrations\Elementor\Widgets;

use Elementor\Controls_Manager;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode;

class ElementorSiteReviews extends ElementorWidget
{
    public function get_icon(): string
    {
        return 'eicon-glsr-reviews';
    }

    public static function shortcodeClass(): string
    {
        return SiteReviewsShortcode::class;
    }

    protected function transformControl(string $name, array $args): array
    {
        $control = parent::transformControl($name, $args);
        if ('pagination' === $name) {
            $icons = [
                'ajax' => 'eicon eicon-spinner',
                'loadmore' => 'eicon eicon-button',
                'true' => 'eicon eicon-redo',
            ];
            $control['label'] = _x('Pagination', 'admin-text', 'site-reviews');
            $control['label_block'] = false;
            $control['type'] = Controls_Manager::CHOOSE;
            foreach ($control['options'] as $key => $value) {
                $control['options'][$key] = [
                    'icon' => $icons[$key] ?? $icons['ajax'],
                    'title' => $value,
                ];
            }
        }
        return $control;
    }
}
