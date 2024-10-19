<?php

namespace GeminiLabs\SiteReviews\Integrations\WooCommerce\Widgets;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Rating;
use GeminiLabs\SiteReviews\Modules\Style;

class WidgetRatingFilter extends \WC_Widget_Rating_Filter
{
    /**
     * @param array $args
     * @param array $instance
     *
     * @return void
     */
    public function widget($args, $instance)
    {
        if (!$this->isWidgetVisible()) {
            return;
        }
        if ($filters = $this->filteredProductCounts()) {
            ob_start();
            $this->widget_start($args, $instance);
            glsr()->render('templates/woocommerce/widgets/rating-filter', [
                'filters' => $filters,
                'style' => 'glsr glsr-'.glsr(Style::class)->styleClasses(),
            ]);
            $this->widget_end($args);
            echo ob_get_clean(); // WPCS: XSS ok.
        }
    }

    /**
     * @return array
     */
    protected function filteredProductCounts()
    {
        $averages = $this->productAverages();
        $baseUrl = remove_query_arg('paged', $this->get_current_page_url());
        $filteredRatings = Cast::toString(wp_unslash(filter_input(INPUT_GET, 'rating_filter')));
        $filteredRatings = explode(',', $filteredRatings);
        $filteredRatings = Arr::uniqueInt($filteredRatings);
        $filters = [];
        foreach ($averages as $rating => $count) {
            if (empty($count)) {
                continue;
            }
            $isFiltered = in_array($rating, $filteredRatings, true);
            $linkRatings = call_user_func($isFiltered ? 'array_diff' : 'array_merge', $filteredRatings, [$rating]);
            $linkRatings = implode(',', $linkRatings);
            $link = $linkRatings ? add_query_arg('rating_filter', $linkRatings, $baseUrl) : remove_query_arg('rating_filter');
            $link = apply_filters('woocommerce_rating_filter_link', $link);
            $countHtml = apply_filters('woocommerce_rating_filter_count', "({$count})", $count, $rating);
            $countHtml = wp_kses($countHtml, ['em' => [], 'span' => [], 'strong' => []]);
            $starsHtml = glsr_star_rating($rating, $count, [
                'theme' => glsr_get_option('integrations.woocommerce.style'),
            ]);
            $filters[] = glsr()->args([
                'classes' => esc_attr('wc-layered-nav-rating'.($isFiltered ? ' chosen' : '')),
                'count' => $countHtml,
                'permalink' => esc_url($link),
                'stars' => str_replace(['<div', 'div>'], ['<span', 'span>'], $starsHtml),
            ]);
        }
        return $filters;
    }

    /**
     * @return bool
     */
    protected function isWidgetVisible()
    {
        if (!is_shop() && !is_product_taxonomy()) {
            return false;
        }
        if (!WC()->query->get_main_query()->post_count) {
            return false;
        }
        return true;
    }

    /**
     * @return array
     */
    protected function productAverages()
    {
        $sql = glsr(Query::class)->sql("
            SELECT apt.post_id AS product_id, ROUND(AVG(r.rating)) AS average
            FROM table|ratings AS r 
            INNER JOIN table|assigned_posts AS apt ON (apt.rating_id = r.ID)
            INNER JOIN table|posts AS p ON (p.ID = apt.post_id AND p.post_type IN ('product'))
            WHERE 1=1 
            AND apt.is_published = 1 
            AND r.is_approved = 1 
            AND r.rating > 0 
            AND r.type = 'local'
            GROUP BY apt.post_id
        ");
        $products = glsr(Database::class)->dbGetResults($sql);
        $averages = array_reverse(glsr(Rating::class)->emptyArray(), true); // preserve keys
        foreach ($products as $product) {
            ++$averages[$product->average];
        }
        return $averages;
    }
}
