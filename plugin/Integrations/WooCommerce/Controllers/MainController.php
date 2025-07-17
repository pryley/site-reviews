<?php

namespace GeminiLabs\SiteReviews\Integrations\WooCommerce\Controllers;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Integrations\WooCommerce\Elementor\Widgets\ProductRating;
use GeminiLabs\SiteReviews\Integrations\WooCommerce\Widgets\WidgetRatingFilter;
use GeminiLabs\SiteReviews\Integrations\WooCommerce\Widgets\WidgetRecentReviews;
use GeminiLabs\SiteReviews\Modules\SchemaParser;
use GeminiLabs\SiteReviews\Review;

class MainController extends AbstractController
{
    /**
     * @action admin_enqueue_scripts
     */
    public function enqueueInlineAdminStyles(): void
    {
        $css = '.woocommerce-review-activity-card .woocommerce-activity-card__actions button.is-tertiary:not(.is-destructive) {display:none}';
        wp_add_inline_style('wc-admin-app', $css);
    }

    /**
     * @filter site-reviews/enqueue/public/inline-styles
     */
    public function filterInlineStyles(string $css): string
    {
        $css .= 'ul.glsr li a{display:flex;justify-content:space-between;}'; // fix rating filter widget
        $css .= '.glsr.woocommerce-product-rating{align-items:center;display:inline-flex;gap:.5em;}';
        $css .= '.glsr.woocommerce-product-rating .woocommerce-review-link{top:-1px!important;}'; // fix product title rating position
        $style = glsr_get_option('integrations.woocommerce.style');
        $colors = [
            'black' => '#212121',
            'woocommerce' => '#96588A',
        ];
        if (!array_key_exists($style, $colors)) {
            return $css;
        }
        $css = str_replace('assets/images/stars/default/', "assets/images/stars/{$style}/", $css);
        $css .= ".glsr:not([data-theme]) .glsr-bar-background-percent{--glsr-bar-bg:{$colors[$style]};}";
        return $css;
    }

    /**
     * @return int
     *
     * @filter woocommerce_product_reviews_pending_count
     */
    public function filterMenuPendingCount()
    {
        return 0;
    }

    /**
     * @param string $status
     * @param string $postType
     * @param string $commentType
     *
     * @return string
     *
     * @filter get_default_comment_status
     */
    public function filterProductCommentStatus($status, $postType, $commentType)
    {
        if ('product' === $postType && 'comment' === $commentType) {
            return 'open';
        }
        return $status;
    }

    /**
     * @param array  $settings
     * @param string $section
     *
     * @return array
     *
     * @filter woocommerce_get_settings_products
     */
    public function filterProductSettings($settings, $section)
    {
        if (!empty($section)) {
            return $settings;
        }
        $disabled = ['woocommerce_enable_review_rating', 'woocommerce_review_rating_required'];
        foreach ($settings as &$setting) {
            if (in_array(Arr::get($setting, 'id'), $disabled)) {
                $setting = Arr::set($setting, 'custom_attributes.disabled', true);
                $setting['desc'] = sprintf('%s <span class="required">(%s)</span>',
                    $setting['desc'],
                    _x('managed by Site Reviews', 'admin-text', 'site-reviews')
                );
            }
        }
        return $settings;
    }

    /**
     * @filter site-reviews/enqueue/public/inline-script/after
     */
    public function filterPublicInlineScript(string $script): string
    {
        $script .= '"undefined"!==typeof jQuery&&(jQuery(".wc-tabs .reviews_tab a").on("click",function(){setTimeout(function(){GLSR.Event.trigger("site-reviews-themes/swiper/resize")},25)}));';
        return $script;
    }

    /**
     * @filter site-reviews/schema/generate
     */
    public function filterRankmathSchemaPreview(array $data, SchemaParser $parser): array
    {
        if (!did_action('rank_math/json_ld/preview')) {
            return $data; // only run this for the preview
        }
        if (!$url = wp_get_referer()) {
            return $data;
        }
        $urlQuery = (string) wp_parse_url($url, \PHP_URL_QUERY);
        $query = wp_parse_args($urlQuery);
        $postId = Cast::toInt($query['post'] ?? 0);
        if (!$product = wc_get_product($postId)) {
            return $data;
        }
        if (!$product->get_reviews_allowed()) {
            return $data;
        }
        return $parser->buildReviewSchema([
            'assigned_posts' => $postId,
        ]);
    }

    /**
     * @return string
     *
     * @filter option_woocommerce_enable_review_rating
     * @filter option_woocommerce_review_rating_required
     */
    public function filterRatingOption()
    {
        return 'yes';
    }

    /**
     * @param \GeminiLabs\SiteReviews\Modules\Html\Tags\ReviewAuthorTag $tag
     *
     * @filter site-reviews/review/value/author
     */
    public function filterReviewAuthorTagValue(string $value, $tag): string
    {
        if ($tag->review->hasVerifiedOwner() && 'yes' === get_option('woocommerce_review_rating_verification_label')) { // @phpstan-ignore-line
            $text = esc_attr__('verified owner', 'site-reviews');
            $value = sprintf('%s <em class="woocommerce-review__verified verified">(%s)</em>', $value, $text);
        }
        return $value;
    }

    /**
     * @filter site-reviews/review/call/hasVerifiedOwner
     */
    public function filterReviewCallbackHasVerifiedOwner(Review $review): bool
    {
        $verified = get_post_meta($review->ID, '_verified', true);
        if ('' !== $verified) {
            return (bool) $verified;
        }
        $review->refresh(); // refresh the review first!
        $verified = false;
        foreach ($review->assigned_posts as $postId) {
            if ('product' === get_post_type($postId)) {
                $verified = wc_customer_bought_product($review->email, $review->author_id, $postId);
                break; // only check the first product
            }
        }
        update_post_meta($review->ID, '_verified', (int) $verified);
        return $verified;
    }

    /**
     * @filter site-reviews/review/call/product
     */
    public function filterReviewCallbackProduct(Review $review): ?\WC_Product
    {
        foreach ($review->assigned_posts as $postId) {
            if ('product' !== get_post_type($postId)) {
                continue;
            }
            if ($product = wc_get_product($postId)) {
                return $product; // only return the first found product
            }
        }
        return null;
    }

    /**
     * @action admin_init
     */
    public function redirectProductReviews(): void
    {
        global $pagenow;
        if ('edit.php' === $pagenow
            && 'product' === filter_input(INPUT_GET, 'post_type')
            && 'product-reviews' === filter_input(INPUT_GET, 'page')) {
            wp_redirect(add_query_arg('notice', 'product-reviews', glsr_admin_url()), 301);
            exit;
        }
    }

    /**
     * @action elementor/widgets/register
     */
    public function registerElementorWidgets(): void
    {
        $widgets = \Elementor\Plugin::instance()->widgets_manager;
        $widgets->unregister('woocommerce-product-rating');
        if (class_exists('ElementorPro\Modules\Woocommerce\Widgets\Product_Rating')) {
            $widgets->register(new ProductRating());
        }
    }

    /**
     * @action widgets_init
     */
    public function registerWidgets(): void
    {
        unregister_widget('WC_Widget_Recent_Reviews');
        unregister_widget('WC_Widget_Rating_Filter');
        register_widget(WidgetRecentReviews::class);
        register_widget(WidgetRatingFilter::class);
    }

    /**
     * @param array $args
     *
     * @return array
     *
     * @action woocommerce_register_post_type_product
     */
    public function removeWoocommerceReviews($args)
    {
        if (array_key_exists('supports', $args)) {
            $args['supports'] = array_diff($args['supports'], ['comments']);
        }
        return $args;
    }

    /**
     * @action admin_notices
     */
    public function renderNotice(): void
    {
        $screen = glsr_current_screen();
        if ('edit' !== $screen->base || 'edit-site-review' !== $screen->id) {
            return;
        }
        if ('product-reviews' !== filter_input(INPUT_GET, 'notice')) {
            return;
        }
        glsr()->render('integrations/woocommerce/notices/reviews');
    }

    /**
     * @action site-reviews/review/created
     */
    public function verifyProductOwner(Review $review): void
    {
        $review->hasVerifiedOwner();
    }
}
