<?php

namespace GeminiLabs\SiteReviews\Integrations\WooCommerce\Controllers;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Contracts\ControllerContract;
use GeminiLabs\SiteReviews\Database\CountManager;
use GeminiLabs\SiteReviews\Defaults\SiteReviewsDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\HookProxy;
use GeminiLabs\SiteReviews\Integrations\WooCommerce\Metaboxes\ReviewsMetabox;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Modules\Rating;
use GeminiLabs\SiteReviews\Modules\Sanitizer;
use GeminiLabs\SiteReviews\Modules\Schema;
use GeminiLabs\SiteReviews\Modules\Style;

class ProductController implements ControllerContract
{
    use HookProxy;

    /**
     * @param string $template
     *
     * @filter comments_template
     */
    public function filterCommentsTemplate($template): string
    {
        if (current_theme_supports('woocommerce') && 'product' === get_post_type()) {
            return glsr()->path('views/integrations/woocommerce/overrides/single-product-reviews.php');
        }
        return Cast::toString($template);
    }

    /**
     * @param string $html
     * @param int    $rating
     * @param int    $count
     *
     * @filter woocommerce_product_get_rating_html
     */
    public function filterGetRatingHtml($html, $rating, $count): string
    {
        if (str_contains($html, 'wc-block')) {
            $html = str_replace('wc-block-grid__product-rating__stars', '', $html);
            return $html;
        }
        $starsHtml = glsr_star_rating($rating, $count, [
            'theme' => glsr_get_option('integrations.woocommerce.style'),
        ]);
        return glsr(Builder::class)->div([
            'class' => glsr(Style::class)->styleClasses(),
            'text' => $starsHtml,
        ]);
    }

    /**
     * @param string $html
     * @param int    $rating
     * @param int    $count
     *
     * @filter woocommerce_get_star_rating_html
     */
    public function filterGetStarRatingHtml($html, $rating, $count): string
    {
        return glsr_star_rating($rating, $count, [
            'theme' => glsr_get_option('integrations.woocommerce.style'),
        ]);
    }

    /**
     * @param mixed       $value
     * @param \WC_Product $product
     *
     * @filter woocommerce_product_get_average_rating
     */
    public function filterProductAverageRating($value, $product): float
    {
        return Cast::toFloat(get_post_meta($product->get_id(), CountManager::META_AVERAGE, true));
    }

    /**
     * @param array $tabs
     *
     * @filter woocommerce_product_data_tabs
     */
    public function filterProductDataTabs($tabs): array
    {
        $tabs = Arr::consolidate($tabs);
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
     *
     * @filter woocommerce_product_query_meta_query
     */
    public function filterProductMetaQuery($metaQuery): array
    {
        global $wp_query;
        $metaQuery = Arr::consolidate($metaQuery);
        $orderby = filter_input(INPUT_GET, 'orderby');
        if (!$orderby && !is_search()) {
            $orderby = apply_filters('woocommerce_default_catalog_orderby', get_option('woocommerce_default_catalog_orderby'));
        }
        if ('rating' !== $orderby) {
            return $metaQuery;
        }
        if ('bayesian' === glsr_get_option('integrations.woocommerce.sorting')) {
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
     * @param array  $args
     * @param string $orderby
     *
     * @filter woocommerce_get_catalog_ordering_args
     */
    public function filterProductPostClauses($args, $orderby): array
    {
        $args = Arr::consolidate($args);
        if ('rating' === $orderby) {
            remove_filter('posts_clauses', [WC()->query, 'order_by_rating_post_clauses']);
        }
        return $args;
    }

    /**
     * @param mixed       $value
     * @param \WC_Product $product
     *
     * @filter woocommerce_product_get_rating_counts
     */
    public function filterProductRatingCounts($value, $product): array
    {
        return glsr_get_ratings(['assigned_posts' => $product->get_id()])->ratings;
    }

    /**
     * @param mixed       $value
     * @param \WC_Product $product
     *
     * @filter woocommerce_product_get_review_count
     */
    public function filterProductReviewCount($value, $product): int
    {
        return Cast::toInt(get_post_meta($product->get_id(), CountManager::META_REVIEWS, true));
    }

    /**
     * @param array $tabs
     *
     * @filter woocommerce_product_tabs
     */
    public function filterProductTabs($tabs): array
    {
        global $product;
        $tabs = Arr::consolidate($tabs);
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
     *
     * @filter woocommerce_product_query_tax_query
     */
    public function filterProductTaxQuery($taxQuery): array
    {
        $taxQuery = Arr::consolidate($taxQuery);
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
     * @param array       $markup
     * @param \WC_Product $product
     *
     * @filter woocommerce_structured_data_product
     */
    public function filterStructuredData($markup, $product): array
    {
        $args = glsr(SiteReviewsDefaults::class)->merge([
            'assigned_posts' => $product->get_id(),
            'display' => 5, // only get the latest 5 reviews
            'rating' => 1, // minimum rating
        ]);
        $markup = Arr::consolidate($markup);
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
     *
     * @filter woocommerce_top_rated_products_widget_args
     */
    public function filterWidgetArgsTopRatedProducts($args): array
    {
        $args = Arr::consolidate($args);
        if ('bayesian' === glsr_get_option('integrations.woocommerce.sorting')) {
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
     *
     * @filter wc_get_template
     */
    public function filterWoocommerceTemplate($template, $templateName): string
    {
        if ('loop/rating.php' === $templateName) {
            return glsr()->path('views/integrations/woocommerce/overrides/loop-rating.php');
        }
        if ('single-product-reviews.php' === $templateName) {
            return glsr()->path('views/integrations/woocommerce/overrides/single-product-reviews.php');
        }
        return Cast::toString($template);
    }

    /**
     * @param \WP_Query $query
     *
     * @action pre_get_posts
     */
    public function modifyProductQuery($query): void
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
        if ('bayesian' === glsr_get_option('integrations.woocommerce.sorting')) {
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
     * @param \WP_Post $post
     *
     * @action add_meta_boxes_product
     */
    public function registerMetaBoxes($post): void
    {
        glsr(ReviewsMetabox::class)->register($post);
    }

    /**
     * @param string $columnName
     * @param string $postType
     *
     * @action bulk_edit_custom_box
     */
    public function renderBulkEditField($columnName, $postType): void
    {
        if ('price' === $columnName && 'product' === $postType) {
            glsr()->render('views/integrations/woocommerce/bulk-edit');
        }
    }

    /**
     * @action woocommerce_after_shop_loop_item_title
     */
    public function renderLoopRating(): void
    {
        global $product;
        if (!wc_review_ratings_enabled()) {
            return;
        }
        $ratings = glsr_get_ratings(['assigned_posts' => 'post_id']);
        if (0 >= $ratings->average && !glsr_get_option('integrations.woocommerce.display_empty', false, 'bool')) {
            return;
        }
        glsr(Template::class)->render('templates/woocommerce/loop/rating', [
            'product' => $product,
            'ratings' => $ratings,
            'style' => glsr(Style::class)->styleClasses(),
            'theme' => glsr_get_option('integrations.woocommerce.style'),
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
     * @param string $columnName
     * @param string $postType
     *
     * @action quick_edit_custom_box
     */
    public function renderQuickEditField($columnName, $postType): void
    {
        if ('price' === $columnName && 'product' === $postType) {
            glsr()->render('views/integrations/woocommerce/quick-edit');
        }
    }

    /**
     * @callback filterProductTabs
     */
    public function renderReviews(): void
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
     * @action woocommerce_single_product_summary
     */
    public function renderTitleRating(): void
    {
        global $product;
        $ratings = glsr_get_ratings(['assigned_posts' => 'post_id']);
        if (0 >= $ratings->average && !glsr_get_option('integrations.woocommerce.display_empty', false, 'bool')) {
            return;
        }
        glsr(Template::class)->render('templates/woocommerce/rating', [
            'product' => $product,
            'ratings' => $ratings,
            'style' => glsr(Style::class)->styleClasses(),
            'theme' => glsr_get_option('integrations.woocommerce.style'),
        ]);
    }

    /**
     * This updates the product_visibility rated-* categories.
     *
     * @action site-reviews/ratings/count/post
     */
    public function updateProductRatingCounts(int $postId, Arguments $counts): void
    {
        if ('product' === get_post_type($postId)) {
            $product = wc_get_product($postId);
            $product->set_rating_counts($counts->ratings);
            $product->set_average_rating($counts->average);
            $product->set_review_count($counts->reviews);
            $product->save();
        }
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
        return glsr_get_option("integrations.woocommerce.{$key}");
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
