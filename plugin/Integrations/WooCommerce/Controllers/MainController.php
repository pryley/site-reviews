<?php

namespace GeminiLabs\SiteReviews\Integrations\WooCommerce\Controllers;

use GeminiLabs\SiteReviews\Controllers\Controller as BaseController;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Integrations\WooCommerce\Elementor\Widgets\ProductRating;
use GeminiLabs\SiteReviews\Integrations\WooCommerce\Widgets\WidgetRatingFilter;
use GeminiLabs\SiteReviews\Integrations\WooCommerce\Widgets\WidgetRecentReviews;
use GeminiLabs\SiteReviews\Review;

class MainController extends BaseController
{
    public const VERIFIED_META_KEY = '_verified';

    /**
     * @filter site-reviews/enqueue/public/inline-styles
     */
    public function filterInlineStyles(string $style): string
    {
        $style .= 'ul.glsr li a{display:flex;justify-content:space-between;}'; // fix rating filter widget
        $style .= '.glsr.woocommerce-product-rating{align-items:center;display:inline-flex;gap:.5em;}.glsr.woocommerce-product-rating .woocommerce-review-link{top:-1px!important;}'; // fix product title rating position
        if ('black' === glsr_get_option('addons.woocommerce.style')) {
            $style .= '.glsr:not([data-theme]) .glsr-bar-background-percent{color:#212121!important;}';
        }
        if ('woocommerce' === glsr_get_option('addons.woocommerce.style')) {
            $style .= '.glsr:not([data-theme]) .glsr-bar-background-percent{color:#96588A!important;}';
        }
        return $style;
    }

    /**
     * @param string $status
     * @param string $postType
     * @param string $commentType
     * @return string
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
     * @param array $settings
     * @param string $section
     * @return array
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
     * @return string
     * @filter option_woocommerce_enable_review_rating
     * @filter option_woocommerce_review_rating_required
     */
    public function filterRatingOption()
    {
        return 'yes';
    }

    /**
     * @return \WC_Product|false
     * @filter site-reviews/review/call/product
     */
    public function filterReviewProductMethod(Review $review)
    {
        if ($product = wc_get_product(Arr::get($review->assigned_posts, 0))) {
            return $product;
        }
        return false;
    }

    /**
     * @param \GeminiLabs\SiteReviews\Modules\Html\Tags\ReviewAuthorTag $tag
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
     * @filter site-reviews/config/inline-styles
     */
    public function filterStarImages(array $config): array
    {
        $style = glsr_get_option('addons.woocommerce.style');
        if (in_array($style, ['black', 'woocommerce'])) {
            $config[':star-empty'] = glsr()->url('assets/images/stars/'.$style.'/star-empty.svg');
            $config[':star-full'] = glsr()->url('assets/images/stars/'.$style.'/star-full.svg');
            $config[':star-half'] = glsr()->url('assets/images/stars/'.$style.'/star-half.svg');
        }
        return $config;
    }

    /**
     * @filter site-reviews/review/call/hasVerifiedOwner
     */
    public function hasVerifiedOwner(Review $review): bool
    {
        $verified = get_post_meta($review->ID, static::VERIFIED_META_KEY, true);
        return '' === $verified
            ? $this->verifyProductOwner($review)
            : (bool) $verified;
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
     * @return array
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
        if ('product_page_product-reviews' !== $screen->base || 'edit.php?post_type=product' !== Arr::get($screen, 'parent_file')) {
            return;
        }
        glsr()->render('integrations/woocommerce/notices/reviews');
    }

    /**
     * @return void|bool
     * @see $this->hasVerifiedOwner()
     * @action site-reviews/review/created
     */
    public function verifyProductOwner(Review $review)
    {
        $review = glsr_get_review($review->ID); // load a fresh instance of the review
        $verified = false;
        foreach ($review->assigned_posts as $postId) {
            if ('product' === get_post_type($postId)) {
                $verified = wc_customer_bought_product($review->email, $review->author_id, $postId);
                break;
            }
        }
        update_post_meta($review->ID, static::VERIFIED_META_KEY, (int) $verified);
        return $verified;
    }
}
