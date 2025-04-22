<?php

namespace GeminiLabs\SiteReviews\Integrations\SureCart\Controllers;

use GeminiLabs\SiteReviews\Contracts\ControllerContract;
use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Database\CountManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Svg;
use GeminiLabs\SiteReviews\HookProxy;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\Tags\ReviewTag;
use GeminiLabs\SiteReviews\Modules\SchemaParser;
use GeminiLabs\SiteReviews\Review;
use SureCart\Models\Product;
use SureCart\Models\Purchase;
use SureCart\Models\User;

class ProductController implements ControllerContract
{
    use HookProxy;

    /**
     * @filter render_block_core/shortcode
     */
    public function filterAssignedPostsPostId(string $content, array $parsedBlock, ?\WP_Block $parentBlock): string
    {
        $postId = $parentBlock->context['postId'] ?? 0;
        $postType = $parentBlock->context['postType'] ?? '';
        if ('sc_product' !== $postType) {
            return $content;
        }
        if (!str_contains($content, '[site_review')) {
            return $content;
        }
        if (!str_contains($content, 'assigned_posts')) {
            return $content;
        }
        if (!str_contains($content, 'post_id')) {
            return $content;
        }
        $pattern = '/(assigned_posts\s*=\s*(["\']?))(.*?)\2(?=\s|$)/';
        return preg_replace_callback($pattern, function ($match) use ($postId) {
            $value = preg_replace('/\bpost_id\b/', $postId, $match[3]);
            return $match[1].$value.$match[2];
        }, $content);
    }

    /**
     * @filter block_type_metadata_settings
     */
    public function filterBlockRenderCallback(array $settings, array $metadata): array
    {
        $name = $metadata['name'] ?? '';
        $targets = [
            'surecart/product-list-sort',
            'surecart/product-list-sort-radio-group-template',
        ];
        if (!in_array($name, $targets)) {
            return $settings;
        }
        $controllerPath = wp_normalize_path(
            realpath(dirname($metadata['file']).'/'.remove_block_asset_path_prefix('file:./controller.php'))
        );
        if (!file_exists($controllerPath)) {
            return $settings;
        }
        $settings['render_callback'] = static function ($attributes, $content, $block) use ($controllerPath, $metadata) {
            $view = require $controllerPath;
            $templatePath = wp_normalize_path(
                realpath(dirname($metadata['file']).'/'.remove_block_asset_path_prefix($view))
            );
            if (isset($options)
                && isset($params)
                && isset($query_order)
                && isset($query_order_by)) {
                $options[] = [
                    'checked' => 'asc' === $query_order && 'rating' === $query_order_by,
                    'href' => $params->addArg('order', 'asc')->addArg('orderby', 'rating')->url(),
                    'label' => esc_html__('Rating, low to high', 'site-reviews'),
                    'value' => 'rating:asc',
                ];
                $options[] = [
                    'checked' => 'desc' === $query_order && 'rating' === $query_order_by,
                    'href' => $params->addArg('order', 'desc')->addArg('orderby', 'rating')->url(),
                    'label' => esc_html__('Rating, high to low', 'site-reviews'),
                    'value' => 'rating:desc',
                ];
            }
            ob_start();
            require $templatePath;
            return ob_get_clean();
        };
        return $settings;
    }

    /**
     * @param string[] $columns
     *
     * @filter manage_sc-products_columns
     */
    public function filterProductColumns(array $columns): array
    {
        $svg = Svg::get('assets/images/icon.svg', [
            'height' => 24,
            'style' => 'display:flex; flex-shrink:0; margin: -4px 0;',
        ]);
        $columns[glsr()->prefix.'rating'] = glsr(Builder::class)->div([
            'style' => 'display:flex; align-items:center; justify-content:center;',
            'text' => sprintf('%s<span>%s</span>', $svg, _x('Reviews', 'admin-text', 'site-reviews')),
        ]);
        return $columns;
    }

    /**
     * @filter surecart/product/json_schema
     */
    public function filterProductSchema(array $schema): array
    {
        $data = glsr(SchemaParser::class)->generate();
        $aggregateRatingSchema = Arr::get($data, 'aggregateRating');
        $reviewSchema = Arr::get($data, 'review');
        if ($aggregateRatingSchema) {
            $schema['aggregateRating'] = $aggregateRatingSchema;
        }
        if ($reviewSchema) {
            $schema['review'] = $reviewSchema;
        }
        // remove Site Reviews generated schema
        add_filter('site-reviews/schema/all', '__return_empty_array');
        return $schema;
    }

    /**
     * @filter site-reviews/review/value/author
     */
    public function filterReviewAuthorTagValue(string $value, ReviewTag $tag): string
    {
        $ownership = glsr_get_option('integrations.surecart.ownership', [], 'array');
        if (!in_array('labeled', $ownership)) {
            return $value;
        }
        if ($tag->review->hasProductOwner()) {
            $text = esc_attr__('verified owner', 'site-reviews');
            $value = sprintf('%s <em data-verified-owner="1">(%s)</em>', $value, $text);
        }
        return $value;
    }

    /**
     * @filter site-reviews/review/call/hasProductOwner
     */
    public function filterReviewCallbackHasProductOwner(Review $review): bool
    {
        $verified = get_post_meta($review->ID, '_sc_verified', true);
        if ('' !== $verified) {
            return (bool) $verified;
        }
        $review->refresh(); // refresh the review first!
        $verified = false;
        foreach ($review->assigned_posts as $postId) {
            if ('sc_product' === get_post_type($postId)) {
                $verified = $this->isProductOwner($review->author_id, $postId);
                break; // only check the first product
            }
        }
        update_post_meta($review->ID, '_sc_verified', (int) $verified);
        return $verified;
    }

    /**
     * @filter site-reviews/build/template/reviews-form
     */
    public function filterReviewFormBuild(string $template, array $data): string
    {
        if ('sc_product' !== get_post_type()) {
            return $template;
        }
        $ownership = glsr_get_option('integrations.surecart.ownership', [], 'array');
        if (!in_array('restricted', $ownership)) {
            return $template;
        }
        if ($this->isProductOwner(get_current_user_id(), get_the_ID())) {
            return $template;
        }
        return glsr(Builder::class)->p([
            'text' => esc_html__('Only logged in customers who have purchased this product may leave a review.', 'woocommerce'),
        ]);
    }

    /**
     * @filter site-reviews/shortcode/site_reviews/attributes
     * @filter site-reviews/shortcode/site_reviews_form/attributes
     * @filter site-reviews/shortcode/site_reviews_summary/attributes
     */
    public function filterShortcodeAttributes(array $attributes, ShortcodeContract $shortcode): array
    {
        $refererQuery = wp_parse_args(wp_parse_url((string) wp_get_referer(), \PHP_URL_QUERY));
        $template = $refererQuery['p'] ?? ''; // Get the current Site Editor template
        if (!str_starts_with((string) $template, '/wp_template/surecart/') && 'sc_product' !== get_post_type()) {
            return $attributes;
        }
        if ($style = glsr_get_option('integrations.surecart.style')) {
            $attributes['data-style'] = $style;
        }
        return $attributes;
    }

    /**
     * @action parse_query
     */
    public function parseProductQuery(\WP_Query $query): void
    {
        if ('sc_product' !== $query->get('post_type')) {
            return;
        }
        if ('rating' !== $query->get('orderby')) {
            return;
        }
        $metaQuery = $query->get('meta_query', []);
        $order = $query->get('order', 'desc');
        if ('bayesian' === glsr_get_option('integrations.surecart.sorting')) {
            $metaQuery[] = $this->buildMetaQuery('glsr_ranking', CountManager::META_RANKING);
            $query->set('meta_query', $metaQuery);
            $query->set('orderby', ['glsr_ranking' => $order]);
        } else {
            $metaQuery[] = $this->buildMetaQuery('glsr_average', CountManager::META_AVERAGE);
            $metaQuery[] = $this->buildMetaQuery('glsr_reviews', CountManager::META_REVIEWS);
            $query->set('meta_query', $metaQuery);
            $query->set('orderby', ['glsr_average' => $order, 'glsr_reviews' => $order]);
        }
    }

    /**
     * @action init
     */
    public function registerBlockPatterns(): void
    {
        register_block_pattern(glsr()->id.'/surecart-product-reviews', [
            'title' => _x('Product Reviews', 'admin-text', 'site-reviews'),
            'categories' => ['surecart_product_page'],
            'blockTypes' => ['surecart/product-page'],
            'priority' => 2,
            'content' => '
                <!-- wp:group {"layout":{"type":"constrained"}} -->
                <div class="wp-block-group">
                    <!-- wp:columns {"align":"wide"} -->
                    <div class="wp-block-columns alignwide">
                        <!-- wp:column {"width":"100%","className":"is-style-default","layout":{"type":"default"}} -->
                        <div class="wp-block-column is-style-default" style="flex-basis:100%">
                            <!-- wp:heading {"className":"is-style-text-subtitle"} -->
                            <h2 class="wp-block-heading is-style-text-subtitle">Reviews</h2>
                            <!-- /wp:heading -->
                            <!-- wp:site-reviews/reviews {"assigned_posts":["post_id"],"hide":["title"],"id":"reviews","pagination":"ajax","schema":1} /-->
                            <!-- wp:heading {"className":"is-style-text-subtitle"} -->
                            <h2 class="wp-block-heading is-style-text-subtitle">Submit a Review</h2>
                            <!-- /wp:heading -->
                            <!-- wp:site-reviews/form {"assigned_posts":["post_id"],"hide":["name","email","title"],"reviews_id":"reviews"} /--></div>
                        <!-- /wp:column -->
                        </div>
                    <!-- /wp:columns -->
                    </div>
                <!-- /wp:group -->',
            // 'postTypes' => ['sc_product'],
            // 'templateTypes' => ['sc_product'],
        ]);
    }

    /**
     * @action init:11
     */
    public function registerBlocks(): void
    {
        register_block_type_from_metadata(glsr()->path('assets/blocks/surecart_product_rating'));
        register_block_type_from_metadata(glsr()->path('assets/blocks/surecart_product_reviews'));
    }

    /**
     * @filter surecart/product/attributes_set
     */
    public function registerProductAttributes(Product $product): void
    {
        $postId = $product->post->ID ?? 0;
        if (0 === $postId) {
            return;
        }
        $ratingInfo = glsr_get_ratings([
            'assigned_posts' => $postId,
        ]);
        $product->setAttribute('rating', $ratingInfo->average);
        $product->setAttribute('reviews', $ratingInfo->reviews);
        $product->setAttribute('ranking', $ratingInfo->ranking);
    }

    /**
     * @action manage_sc-products_custom_column
     */
    public function renderProductColumnValues(string $column, $product): void
    {
        if (glsr()->prefix.'rating' !== $column) {
            return;
        }
        echo glsr(Builder::class)->a([
            'href' => add_query_arg('assigned_post', $product->post->ID, glsr_admin_url()),
            'text' => $product->reviews,
        ]);
    }

    /**
     * @param string $which
     *
     * @action manage_products_extra_tablenav
     */
    public function renderProductTableInlineStyles($which): void
    {
        if ('top' !== $which) {
            return;
        }
        echo '<style>'.
        '@media screen and (min-width: 783px) {'.
            '.fixed .column-glsr_rating { width: 5%; }'.
            'th.column-glsr_rating span { display: none; }'.
            'td.column-glsr_rating { text-align: center; }'.
        '}'.
        '</style>';
    }

    /**
     * @action site-reviews/review/created
     */
    public function verifyProductOwner(Review $review): void
    {
        $review->hasProductOwner();
    }

    protected function buildMetaQuery(string $orderbyKey, string $metaKey): array
    {
        return [
            'relation' => 'OR',
            $orderbyKey => ['key' => $metaKey, 'compare' => 'NOT EXISTS'], // this comes first!
            ['key' => $metaKey, 'compare' => 'EXISTS'],
        ];
    }

    protected function isProductOwner(int $userId, int $productId): bool
    {
        if (!$user = User::getUserBy('id', $userId)) { // @phpstan-ignore-line
            return false;
        }
        if (!$customer = $user->customer()) { // @phpstan-ignore-line
            return false;
        }
        if (!$product = sc_get_product($productId)) {
            return false;
        }
        $purchases = Purchase::where([ // @phpstan-ignore-line
            'customer_ids' => [$customer->id],
            'product_ids' => [$product->id], // @phpstan-ignore-line
        ])->get();
        return !empty($purchases);
    }
}
