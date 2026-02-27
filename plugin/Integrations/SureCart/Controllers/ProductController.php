<?php

namespace GeminiLabs\SiteReviews\Integrations\SureCart\Controllers;

use GeminiLabs\SiteReviews\Contracts\BuilderContract;
use GeminiLabs\SiteReviews\Contracts\ControllerContract;
use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Database\CountManager;
use GeminiLabs\SiteReviews\Database\PostMeta;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Svg;
use GeminiLabs\SiteReviews\HookProxy;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\ReviewForm;
use GeminiLabs\SiteReviews\Modules\Html\Tags\ReviewTag;
use GeminiLabs\SiteReviews\Modules\Paginate;
use GeminiLabs\SiteReviews\Modules\Sanitizer;
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

    public function filterPaginatedLink(array $link, array $args, BuilderContract $builder, Paginate $paginate): array
    {
        $type = $link['type'] ?? '';
        if (!in_array($type, ['next', 'prev'])) {
            return $link;
        }
        $referer = urldecode((string) wp_get_referer());
        if (str_contains($referer, 'site-editor.php')) {
            parse_str(parse_url($referer, PHP_URL_QUERY), $params);
            $template = $params['p'] ?? '';
            if (!str_ends_with($template, 'surecart//single-sc_product')) {
                return $link;
            }
        } else {
            $baseUrl = str_replace('%_%', '', $paginate->args->base);
            $postId = url_to_postid($baseUrl);
            if ('sc_product' !== get_post_type($postId)) {
                return $link;
            }
        }
        if ('prev' === $type) {
            $svg = \SureCart::svg()->get('arrow-left', ['aria-hidden' => true]); // @phpstan-ignore-line
            $svg = wp_kses($svg, sc_allowed_svg_html());
            $args['text'] = $svg.$args['text'];
            if (1 >= $paginate->args->current) {
                $tag = 'span';
            }
        }
        if ('next' === $type) {
            $svg = \SureCart::svg()->get('arrow-right', ['aria-hidden' => true]); // @phpstan-ignore-line
            $svg = wp_kses($svg, sc_allowed_svg_html());
            $args['text'] = $args['text'].$svg;
            if ($paginate->args->current >= $paginate->args->total) {
                $tag = 'span';
            }
        }
        $link['link'] = $builder->build('div', [
            'data-type' => $type,
            'text' => $builder->build($tag ?? 'a', $args),
        ]);
        return $link;
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
        $verified = glsr(PostMeta::class)->get($review->ID, 'sc_verified');
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
        glsr(PostMeta::class)->set($review->ID, 'sc_verified', (int) $verified);
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
        if ($this->isProductOwner(get_current_user_id(), (int) get_the_ID())) {
            return $template;
        }
        return glsr(Builder::class)->p([
            'text' => esc_html__('Only logged in customers who have purchased this product may leave a review.', 'site-reviews'),
        ]);
    }

    /**
     * @param \GeminiLabs\SiteReviews\Modules\Html\ReviewField[] $fields
     *
     * @return \GeminiLabs\SiteReviews\Modules\Html\ReviewField[]
     *
     * @filter site-reviews/review-form/fields/visible
     */
    public function filterReviewFormFields(array $fields, ReviewForm $form): array
    {
        if (!is_user_logged_in()) {
            return $fields;
        }
        if ('sc_product' !== get_post_type()) {
            return $fields;
        }
        $user = wp_get_current_user();
        array_walk($fields, function ($field) use ($form, $user) {
            if (in_array($field->original_name, $form->args()->hide)) {
                return;
            }
            if ('email' === $field->original_name && empty($field->value)) {
                $field->value = glsr(Sanitizer::class)->sanitizeUserEmail($user);
                return;
            }
            if ('name' === $field->original_name && empty($field->value)) {
                $field->value = glsr(Sanitizer::class)->sanitizeUserName($user);
            }
        });
        return $fields;
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
        ])->get(); // @phpstan-ignore-line
        return !empty($purchases);
    }
}
