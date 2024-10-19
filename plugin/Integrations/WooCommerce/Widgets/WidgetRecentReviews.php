<?php

namespace GeminiLabs\SiteReviews\Integrations\WooCommerce\Widgets;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Style;

class WidgetRecentReviews extends \WC_Widget_Recent_Reviews
{
    /**
     * @param array $args
     * @param array $instance
     *
     * @see \WP_Widget
     */
    public function widget($args, $instance)
    {
        if ($this->get_cached_widget($args)) {
            return;
        }
        ob_start();
        $number = Arr::get($instance, 'number', $this->settings['number']['std']);
        $reviews = glsr_get_reviews([
            'assigned_posts' => 'product',
            'per_page' => $number,
        ]);
        if (count($reviews)) {
            $this->widget_start($args, $instance);
            glsr()->render('templates/woocommerce/widgets/recent-reviews', [
                'args' => $args,
                'reviews' => $reviews,
                'style' => 'glsr glsr-'.glsr(Style::class)->styleClasses(),
                'theme' => glsr_get_option('integrations.woocommerce.style'),
            ]);
            $this->widget_end($args);
        }
        $content = ob_get_clean();
        echo $content; // WPCS: XSS ok
        $this->cache_widget($args, $content);
    }
}
