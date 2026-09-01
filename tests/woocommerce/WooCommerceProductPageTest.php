<?php

use GeminiLabs\SiteReviews\Database\PostMeta;
use GeminiLabs\SiteReviews\Integrations\WooCommerce\Controllers\ProductController;

use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\enableWooCommerceIntegration;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;
use function GeminiLabs\SiteReviews\Tests\wooCompletedOrder;
use function GeminiLabs\SiteReviews\Tests\wooProduct;
use function GeminiLabs\SiteReviews\Tests\wooResetProductData;
use function GeminiLabs\SiteReviews\Tests\wooSetupProductData;

/*
 * The single product page: the reviews tab, what it renders, who gets the form,
 * and the verified-owner badge. Every one of these reads a WooCommerce return
 * value — the product's review settings, wc_customer_bought_product() against
 * real orders, the template loader — so none of them can run against the stubs.
 */

beforeEach(function () {
    resetPluginState();
    enableWooCommerceIntegration();
});

afterEach(function () {
    wooResetProductData();
});

function wooRenderedReviewsTab(): string
{
    ob_start();
    glsr(ProductController::class)->renderSingleProductReviews();
    return (string) ob_get_clean();
}

test('the reviews tab is the plugin\'s, with the plugin\'s count', function () {
    $product = wooProduct(['description' => 'A kettle.']); // WooCommerce's description tab needs one
    createReview(['assigned_posts' => $product->get_id()]);
    createReview(['assigned_posts' => $product->get_id()]);
    wooSetupProductData($product);

    $tabs = apply_filters('woocommerce_product_tabs', []);

    expect($tabs['reviews']['title'])->toBe('Reviews (2)')
        ->and($tabs['reviews']['priority'])->toBe(30)
        ->and($tabs['reviews']['callback'][1])->toBe('renderSingleProductReviews');
    expect($tabs)->toHaveKey('description'); // WooCommerce's own tabs are left alone
});

test('the tab renders the plugin\'s summary, reviews and form', function () {
    update_option('woocommerce_review_rating_verification_required', 'no');
    $product = wooProduct(['name' => 'Copper Kettle']);
    createReview(['assigned_posts' => $product->get_id(), 'content' => 'Sturdy, arrived early.']);
    createReview(['assigned_posts' => $product->get_id(), 'content' => 'Boils fast.']);
    wooSetupProductData($product);

    $html = wooRenderedReviewsTab();

    expect($html)
        ->toContain('data-integration="site-reviews"')
        ->toContain('2 reviews for <span>Copper Kettle</span>')
        ->toContain('Sturdy, arrived early.')
        ->toContain('Boils fast.')
        ->toContain('id="review_form"')
        ->toContain('<form');
});

test('a visitor who has not bought the product sees the verification notice, not the form', function () {
    update_option('woocommerce_review_rating_verification_required', 'yes');
    $product = wooProduct();
    wooSetupProductData($product);

    $html = wooRenderedReviewsTab();

    expect($html)
        ->toContain('Only logged in customers who have purchased this product may leave a review.')
        ->not->toContain('id="review_form"');
});

test('a customer who bought the product gets the form', function () {
    update_option('woocommerce_review_rating_verification_required', 'yes');
    $product = wooProduct();
    $userId = createUser(['user_email' => 'jane@example.org']);
    wooCompletedOrder($product, 'jane@example.org', $userId);
    wp_set_current_user($userId);
    wooSetupProductData($product);

    $html = wooRenderedReviewsTab();

    expect($html)
        ->toContain('id="review_form"')
        ->not->toContain('Only logged in customers who have purchased this product may leave a review.');
});

test('a review by a customer who bought the product is verified; anyone else is not', function () {
    $product = wooProduct();
    wooCompletedOrder($product, 'jane@example.org');

    $verified = createReview(['assigned_posts' => $product->get_id(), 'email' => 'jane@example.org']);
    $unverified = createReview(['assigned_posts' => $product->get_id(), 'email' => 'sam@example.org']);

    expect($verified->hasVerifiedOwner())->toBeTrue()
        ->and($unverified->hasVerifiedOwner())->toBeFalse();
    // Answered once, on creation, and remembered on the review.
    expect(glsr(PostMeta::class)->get($verified->ID, 'verified'))->toBe('1')
        ->and(glsr(PostMeta::class)->get($unverified->ID, 'verified'))->toBe('0');
});

test('comments_template is overridden for a product where the theme declares woocommerce support', function () {
    // WooCommerce's own loader answers comments_template() for a product on any theme it
    // treats as supported — every block theme included — with ITS reviews template, straight
    // from its plugin directory (never through wc_get_template, so the plugin's template
    // override cannot see it). The plugin's override runs later and takes over only where
    // the theme itself declares support, which this block theme does not. WooCommerce never
    // calls comments_template() itself: its reviews tab is the string callback
    // 'comments_template', and the plugin replaces that tab, so this filter is reached only by
    // a theme that calls comments_template() directly on a product.
    wooSetupProductData(wooProduct());
    $theme = '/theme/comments.php';

    expect(apply_filters('comments_template', $theme))
        ->toBe(WC()->plugin_path().'/templates/single-product-reviews.php');

    add_theme_support('woocommerce');
    try {
        expect(apply_filters('comments_template', $theme))
            ->toBe(glsr()->path('views/integrations/woocommerce/overrides/single-product-reviews.php'));
    } finally {
        remove_theme_support('woocommerce');
    }
});

test('woocommerce\'s rating template renders the plugin\'s stars and count', function () {
    $product = wooProduct();
    createReview(['assigned_posts' => $product->get_id(), 'rating' => 4]);
    createReview(['assigned_posts' => $product->get_id(), 'rating' => 2]);
    wooSetupProductData($product);

    $html = wc_get_template_html('single-product/rating.php');

    expect($html)
        ->toContain('woocommerce-product-rating')
        ->toContain('glsr-star-rating')
        ->toContain('data-rating="3"')
        ->toContain('<span class="count">2</span> customer reviews');
});
