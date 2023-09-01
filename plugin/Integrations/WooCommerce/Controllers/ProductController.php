<?php

namespace GeminiLabs\SiteReviews\Integrations\WooCommerce\Controllers;

use GeminiLabs\SiteReviews\Database\CountManager;
use GeminiLabs\SiteReviews\Defaults\SiteReviewsDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Modules\Rating;
use GeminiLabs\SiteReviews\Modules\Sanitizer;
use GeminiLabs\SiteReviews\Modules\Schema;
use GeminiLabs\SiteReviews\Modules\Style;

class ProductController
{
    /**
     * @param string $template
     * @return string
     * @filter comments_template
     */
    public function filterCommentsTemplate($template)
    {
        if (current_theme_supports('woocommerce') && 'product' === get_post_type()) {
            return glsr()->path('views/integrations/woocommerce/overrides/single-product-reviews.php');
        }
        return $template;
    }

    /**
     * @param string $html
     * @param int $rating
     * @param int $count
     * @return string
     * @filter woocommerce_product_get_rating_html
     */
    public function filterGetRatingHtml($html, $rating, $count)
    {
        return glsr(Builder::class)->div([
            'class' => 'glsr glsr-'.glsr(Style::class)->styleClasses(),
            'text' => glsr_star_rating($rating, $count, ['theme' => glsr_get_option('addons.woocommerce.style')]),
        ]);
    }

    /**
     * @param string $html
     * @param int $rating
     * @param int $count
     * @return string
     * @filter woocommerce_get_star_rating_html
     */
    public function filterGetStarRatingHtml($html, $rating, $count)
    {
        return glsr_star_rating($rating, $count, ['theme' => glsr_get_option('addons.woocommerce.style')]);
    }

    /**
     * @param mixed $value
     * @param \WC_Product $product
     * @return float
     * @filter woocommerce_product_get_average_rating
     */
    public function filterProductAverageRating($value, $product)
    {
        return Cast::toFloat(get_post_meta($product->get_id(), CountManager::META_AVERAGE, true));
    }

    /**
     * @param array $tabs
     * @return array
     * @filter woocommerce_product_data_tabs
     */
    public function filterProductDataTabs($tabs)
    {
        $tabs[glsr()->id] = [
            'label' => glsr()->name,
            'target' => glsr()->id,
            'priority' => 100,
            'class' => [],
        ];
        return $tabs;
    }

    /**
     * @param array $metaQuery
     * @return array
     * @filter woocommerce_product_query_meta_query
     */
    public function filterProductMetaQuery($metaQuery)
    {
        global $wp_query;
        $orderby = filter_input(INPUT_GET, 'orderby');
        if (!$orderby && !is_search()) {
            $orderby = apply_filters('woocommerce_default_catalog_orderby', get_option('woocommerce_default_catalog_orderby'));
        }
        if ('rating' !== $orderby) {
            return $metaQuery;
        }
        if ('bayesian' === glsr_get_option('addons.woocommerce.sorting')) {
            $metaQuery[] = $this->buildMetaQuery('glsr_ranking', CountManager::META_RANKING);
            $wp_query->set('orderby', ['glsr_ranking' => 'DESC']);
        } else {
            $metaQuery[] = $this->buildMetaQuery('glsr_average', CountManager::META_AVERAGE);
            $metaQuery[] = $this->buildMetaQuery('glsr_reviews', CountManager::META_REVIEWS);
            $wp_query->set('orderby', ['glsr_average' => 'DESC', 'glsr_reviews' => 'DESC']);
        }
        return $metaQuery;
    }

    /**
     * @param array $args
     * @param string $orderby
     * @return array
     * @filter woocommerce_get_catalog_ordering_args
     */
    public function filterProductPostClauses($args, $orderby)
    {
        if ('rating' === $orderby) {
            remove_filter('posts_clauses', [WC()->query, 'order_by_rating_post_clauses']);
        }
        return $args;
    }

    /**
     * @param mixed $value
     * @param \WC_Product $product
     * @return array
     * @filter woocommerce_product_get_rating_counts
     */
    public function filterProductRatingCounts($value, $product)
    {
        return glsr_get_ratings(['assigned_posts' => $product->get_id()])->ratings;
    }

    /**
     * @param mixed $value
     * @param \WC_Product $product
     * @return int
     * @filter woocommerce_product_get_review_count
     */
    public function filterProductReviewCount($value, $product)
    {
        return Cast::toInt(get_post_meta($product->get_id(), CountManager::META_REVIEWS, true));
    }

    /**
     * @param array $tabs
     * @return array
     * @filter woocommerce_product_tabs
     */
    public function filterProductTabs($tabs)
    {
        global $product;
        if ($product instanceof \WC_Product && $product->get_reviews_allowed()) {
            $tabs['reviews'] = [
                'callback' => [$this, 'renderReviews'],
                'priority' => 30,
                'title' => sprintf(__('Reviews (%d)', 'site-reviews'), $product->get_review_count()),
            ];
        }
        return $tabs;
    }

    /**
     * @param array $taxQuery
     * @return array
     * @filter woocommerce_product_query_tax_query
     */
    public function filterProductTaxQuery($taxQuery)
    {
        foreach ($taxQuery as $key => $query) {
            if (!empty($query['rating_filter'])) {
                $filteredRatings = [];
                $field = Arr::get($query, 'field');
                $taxonomy = Arr::get($query, 'taxonomy');
                foreach (Arr::consolidate(Arr::get($query, 'terms')) as $value) {
                    $term = get_term_by($field, $value, $taxonomy);
                    $filteredRatings[] = Cast::toInt(Str::removePrefix(Arr::get($term, 'slug'), 'rated-'));
                }
                unset($taxQuery[$key]);
                break;
            }
        }
        if (!empty($filteredRatings)) {
            $this->setMetaQueriesForFilteredRatings($filteredRatings);
        }
        return $taxQuery;
    }

    /**
     * @param array $markup
     * @param \WC_Product $product
     * @return array
     * @filter woocommerce_structured_data_product
     */
    public function filterStructuredData($markup, $product)
    {
        $args = glsr(SiteReviewsDefaults::class)->merge([
            'assigned_posts' => $product->get_id(),
            'display' => 5, // only get the latest 5 reviews
            'rating' => 1, // minimum rating
        ]);
        $schema = glsr(Schema::class)->build($args, glsr_get_reviews($args));
        if (array_key_exists('review', $schema)) {
            $markup['review'] = $schema['review'];
        } else {
            unset($markup['review']);
        }
        return $markup;
    }

    /**
     * @param array $args
     * @return array
     * @filter woocommerce_top_rated_products_widget_args
     */
    public function filterWidgetArgsTopRatedProducts($args)
    {
        if ('bayesian' === glsr_get_option('addons.woocommerce.sorting')) {
            $args['meta_query'][] = $this->buildMetaQuery('glsr_ranking', CountManager::META_RANKING);
            $args['orderby'] = ['glsr_ranking' => 'DESC'];
        } else {
            $args['meta_query'][] = $this->buildMetaQuery('glsr_average', CountManager::META_AVERAGE);
            $args['meta_query'][] = $this->buildMetaQuery('glsr_reviews', CountManager::META_REVIEWS);
            $args['orderby'] = ['glsr_average' => 'DESC', 'glsr_reviews' => 'DESC'];
        }
        return $args;
    }

    /**
     * @param string $template
     * @param string $templateName
     * @return string
     * @filter wc_get_template
     */
    public function filterWoocommerceTemplate($template, $templateName)
    {
        if ('loop/rating.php' === $templateName) {
            return glsr()->path('views/integrations/woocommerce/overrides/loop-rating.php');
        }
        if ('single-product-reviews.php' === $templateName) {
            return glsr()->path('views/integrations/woocommerce/overrides/single-product-reviews.php');
        }
        return $template;
    }

    /**
     * @param \WP_Query $query
     * @return void
     * @action pre_get_posts
     */
    public function modifyProductQuery($query)
    {
        if (!is_a($query, 'Automattic\WooCommerce\Blocks\Utils\BlocksWpQuery')) {
            return;
        }
        if ('rating' !== $query->get('orderby')) {
            return;
        }
        $metaQuery = $query->get('meta_query');
        if (empty($metaQuery)) {
            $metaQuery = [];
        }
        if ('bayesian' === glsr_get_option('addons.woocommerce.sorting')) {
            $metaQuery[] = $this->buildMetaQuery('glsr_ranking', CountManager::META_RANKING);
            $query->set('meta_query', $metaQuery);
            $query->set('orderby', ['glsr_ranking' => 'DESC']);
        } else {
            $metaQuery[] = $this->buildMetaQuery('glsr_average', CountManager::META_AVERAGE);
            $metaQuery[] = $this->buildMetaQuery('glsr_reviews', CountManager::META_REVIEWS);
            $query->set('meta_query', $metaQuery);
            $query->set('orderby', ['glsr_average' => 'DESC', 'glsr_reviews' => 'DESC']);
        }
    }

    /**
     * @action admin_head
     */
    public function printInlineStyle(): void
    {
        echo '<style type="text/css">#woocommerce-product-data ul.wc-tabs li.site-reviews_tab a::before { content: "\f459"; }</style>';
    }

    /**
     * @return void
     * @action woocommerce_after_shop_loop_item_title
     */
    public function renderLoopRating()
    {
        global $product;
        if (!wc_review_ratings_enabled()) {
            return;
        }
        $ratings = glsr_get_ratings(['assigned_posts' => 'post_id']);
        if (0 >= $ratings->average && 'no' === glsr_get_option('addons.woocommerce.display_empty')) {
            return;
        }
        glsr(Template::class)->render('templates/woocommerce/loop/rating', [
            'product' => $product,
            'ratings' => $ratings,
            'style' => 'glsr glsr-'.glsr(Style::class)->styleClasses(),
            'theme' => glsr_get_option('addons.woocommerce.style'),
        ]);
    }

    /**
     * @action woocommerce_product_data_panels
     */
    public function renderProductDataPanel(): void
    {
        global $product_object;
        glsr(Template::class)->render('views/integrations/woocommerce/product-data-panel', [
            'product' => $product_object,
        ]);
    }

    /**
     * @return void
     * @see $this->filterProductTabs()
     */
    public function renderReviews()
    {
        global $product;
        if ($product instanceof \WC_Product && $product->get_reviews_allowed()) {
            $isVerifiedOwner = wc_customer_bought_product('', get_current_user_id(), $product->get_id());
            glsr(Template::class)->render('templates/woocommerce/reviews', [
                'form' => do_shortcode($this->option($product, 'form')),
                'product' => $product,
                'ratings' => glsr_get_ratings(['assigned_posts' => 'post_id']),
                'reviews' => do_shortcode($this->option($product, 'reviews')),
                'summary' => do_shortcode($this->option($product, 'summary')),
                'verified' => $isVerifiedOwner || 'no' === get_option('woocommerce_review_rating_verification_required'),
            ]);
        }
    }

    /**
     * @return void
     * @action woocommerce_single_product_summary
     */
    public function renderTitleRating()
    {
        global $product;
        $ratings = glsr_get_ratings(['assigned_posts' => 'post_id']);
        if (0 >= $ratings->average && 'no' === glsr_get_option('addons.woocommerce.display_empty')) {
            return;
        }
        glsr(Template::class)->render('templates/woocommerce/rating', [
            'product' => $product,
            'ratings' => $ratings,
            'style' => 'glsr glsr-'.glsr(Style::class)->styleClasses(),
            'theme' => glsr_get_option('addons.woocommerce.style'),
        ]);
    }

    /**
     * @action woocommerce_admin_process_product_object
     */
    public function updateProductData(\WC_Product $product): void
    {
        $shortcodes = [
            'site_reviews',
            'site_reviews_form',
            'site_reviews_summary',
        ];
        foreach ($shortcodes as $shortcode) {
            $value = trim(filter_input(INPUT_POST, $shortcode));
            $value = glsr(Sanitizer::class)->sanitizeText($value);
            if (empty($value)) {
                $product->delete_meta_data($shortcode);
                continue;
            }
            if (1 !== preg_match("/^\[{$shortcode}(\s[^\]]*\]|\])$/", $value)) {
                continue;
            }
            if (!str_contains($value, 'assigned_posts')) {
                $value = str_replace($shortcode, sprintf('%s assigned_posts="post_id"', $shortcode), $value);
            }
            $product->update_meta_data($shortcode, $value);
        }
    }

    protected function buildMetaQuery(string $orderbyKey, string $metaKey): array
    {
        return [
            'relation' => 'OR',
            $orderbyKey => ['key' => $metaKey, 'compare' => 'NOT EXISTS'], // this comes first!
            ['key' => $metaKey, 'compare' => 'EXISTS'],
        ];
    }

    protected function option(\WC_Product $product, string $key): string
    {
        $shortcodes = [
            'form' => 'site_reviews_form',
            'reviews' => 'site_reviews',
            'summary' => 'site_reviews_summary',
        ];
        if (!array_key_exists($key, $shortcodes)) {
            return '';
        }
        if ($override = $product->get_meta($shortcodes[$key])) {
            return $override;
        }
        return glsr_get_option('addons.woocommerce.'.$key);
    }

    /**
     * @param int[] $ratings
     */
    protected function setMetaQueriesForFilteredRatings(array $ratings): void
    {
        global $wp_query;
        $ratings = Arr::uniqueInt($ratings);
        if (!empty($ratings)) {
            $metaQuery = Arr::consolidate($wp_query->get('meta_query'));
            $metaQueries = ['relation' => 'OR'];
            foreach ($ratings as $rating) {
                $metaQueries[] = [
                    'key' => CountManager::META_AVERAGE,
                    'compare' => 'BETWEEN',
                    'value' => [$rating - .5, $rating + .49], // compare the rating to a rounded average range
                ];
            }
            $metaQuery[] = $metaQueries;
            $wp_query->set('meta_query', $metaQuery);
        }
    }
}
