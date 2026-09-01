<?php

use GeminiLabs\SiteReviews\Database\CountManager;

use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\enableWooCommerceIntegration;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;
use function GeminiLabs\SiteReviews\Tests\wooProduct;

/*
 * Rating aggregation through a real WC_Product. When a review is assigned to
 * a product, CountManager::posts() recounts and fires ratings/count/post, and
 * ProductController::updateProductRatingCounts() writes the result into the
 * product through WooCommerce's own setters and save() — the path the stubs
 * cannot take, because wc_get_product() answers null there.
 *
 * Two things are asserted about each write: what WooCommerce PERSISTS (its own
 * meta and the wc_product_meta_lookup row, which its catalog sorting and the
 * Store API read without any filter of ours), and what its getters ANSWER,
 * which the woocommerce_product_get_* filters route to the plugin's counts.
 */

beforeEach(function () {
    resetPluginState();
    enableWooCommerceIntegration();
});

function wooLookupRow(int $productId): ?object
{
    global $wpdb;
    return $wpdb->get_row($wpdb->prepare(
        "SELECT average_rating, rating_count FROM {$wpdb->prefix}wc_product_meta_lookup WHERE product_id = %d",
        $productId
    ));
}

test('a review assigned to a product is written into the product', function () {
    $product = wooProduct();

    createReview(['assigned_posts' => $product->get_id(), 'rating' => 5]);

    $product = wc_get_product($product->get_id());
    expect($product->get_average_rating())->toBe(5.0)
        ->and($product->get_review_count())->toBe(1)
        ->and($product->get_rating_counts()[5])->toBe(1);
    // What WooCommerce stored, read without the plugin's filters.
    expect((float) get_post_meta($product->get_id(), '_wc_average_rating', true))->toBe(5.0)
        ->and((int) get_post_meta($product->get_id(), '_wc_review_count', true))->toBe(1);
    $row = wooLookupRow($product->get_id());
    expect((float) $row->average_rating)->toBe(5.0)
        ->and((int) $row->rating_count)->toBe(1);
});

test('a second review moves the average and the counts', function () {
    $product = wooProduct();

    createReview(['assigned_posts' => $product->get_id(), 'rating' => 5]);
    createReview(['assigned_posts' => $product->get_id(), 'rating' => 3]);

    $product = wc_get_product($product->get_id());
    expect($product->get_average_rating())->toBe(4.0)
        ->and($product->get_review_count())->toBe(2)
        ->and($product->get_rating_counts()[5])->toBe(1)
        ->and($product->get_rating_counts()[3])->toBe(1);
    expect((float) wooLookupRow($product->get_id())->average_rating)->toBe(4.0)
        ->and((int) wooLookupRow($product->get_id())->rating_count)->toBe(2);
});

test('woocommerce answers with the plugin\'s average, not its own stale meta', function () {
    // A product whose WooCommerce meta still holds a comment-era average: the
    // getter's filter reads the plugin's count meta, which does not exist yet.
    $product = wooProduct();
    update_post_meta($product->get_id(), '_wc_average_rating', '1');
    update_post_meta($product->get_id(), '_wc_review_count', '7');

    $product = wc_get_product($product->get_id());

    expect($product->get_average_rating())->toBe(0.0)
        ->and($product->get_review_count())->toBe(0);
    // WooCommerce's own meta is left as it was; the getters just do not read it.
    expect(get_post_meta($product->get_id(), '_wc_average_rating', true))->toBe('1');
});

test('woocommerce\'s rating html is the plugin\'s stars', function () {
    expect(wc_get_rating_html(4, 2))
        ->toContain('glsr-star-rating')
        ->toContain('data-rating="4"')
        ->toContain('data-reviews="2"')
        ->not->toContain('class="star-rating"');
    expect(wc_get_star_rating_html(4, 2))->toContain('glsr-star-rating');
});
