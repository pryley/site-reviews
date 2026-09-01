<?php

use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\enableWooCommerceIntegration;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;
use function GeminiLabs\SiteReviews\Tests\restRequest;
use function GeminiLabs\SiteReviews\Tests\wooProduct;

/*
 * WooCommerce's review routes, answered by the plugin. Three surfaces, three
 * mechanisms: the wc/v3 controllers are swapped for the plugin's subclasses
 * (woocommerce_rest_api_get_rest_namespaces), the Store API and wc-analytics
 * callbacks are rewired in place (rest_endpoints), and wc-analytics only
 * registers when a request for its namespace is dispatched (WooCommerce lazy-
 * loads it on rest_pre_dispatch). Every request here goes through
 * rest_do_request(), so the routes are matched, permission-checked and
 * dispatched exactly as an HTTP request would be.
 */

beforeEach(function () {
    resetPluginState();
    enableWooCommerceIntegration();
    // rest_api_init has not fired for this test: it is a front-end request, not /wp-json.
    $GLOBALS['wp_rest_server'] = new WP_REST_Server();
    do_action('rest_api_init', $GLOBALS['wp_rest_server']);
});

afterEach(function () {
    unset($GLOBALS['wp_rest_server']);
});

function wooActAsAdmin(): int
{
    $userId = createUser(['role' => 'administrator']);
    wp_set_current_user($userId);
    return $userId;
}

function wooReviewedProduct(int $rating = 4): \WC_Product_Simple
{
    $product = wooProduct();
    createReview([
        'assigned_posts' => $product->get_id(),
        'content' => 'Sturdy, arrived early.',
        'email' => 'jane@example.org',
        'name' => 'Jane Doe',
        'rating' => $rating,
    ]);
    return $product;
}

test('the wc/v3 product reviews route lists the plugin\'s reviews', function () {
    wooActAsAdmin();
    $product = wooReviewedProduct();

    $response = restRequest('GET', '/wc/v3/products/reviews');

    expect($response->get_status())->toBe(200)
        ->and($response->get_headers()['X-WP-Total'])->toBe(1);
    $review = $response->get_data()[0];
    expect($review['product_id'])->toBe($product->get_id())
        ->and($review['rating'])->toBe(4)
        ->and($review['reviewer'])->toBe('Jane Doe')
        ->and($review['status'])->toBe('approved')
        ->and($review['review'])->toBe("<p>Sturdy, arrived early.</p>\n")
        ->and($review['verified'])->toBeFalse();
});

test('the wc/v3 product reviews route refuses a visitor', function () {
    wooReviewedProduct();

    $response = restRequest('GET', '/wc/v3/products/reviews');

    expect($response->get_status())->toBe(401)
        ->and($response->get_data()['code'])->toBe('woocommerce_rest_cannot_view');
});

test('a review created through the wc/v3 route is a plugin review', function () {
    wooActAsAdmin();
    $product = wooProduct();

    $response = restRequest('POST', '/wc/v3/products/reviews', [
        'product_id' => $product->get_id(),
        'rating' => 5,
        'review' => 'Boils fast.',
        'reviewer' => 'Sam Doe',
        'reviewer_email' => 'sam@example.org',
    ]);

    expect($response->get_status())->toBe(201);
    $reviews = glsr_get_reviews(['assigned_posts' => $product->get_id()]);
    expect($reviews->total)->toBe(1)
        ->and($reviews->reviews[0]->content)->toBe('Boils fast.')
        ->and($reviews->reviews[0]->rating)->toBe(5);
    expect($response->get_headers()['Location'])
        ->toEndWith("/wc/v3/products/reviews/{$reviews->reviews[0]->ID}");
});

test('the reviews totals report counts the plugin\'s ratings', function () {
    wooActAsAdmin();
    $product = wooProduct();
    createReview(['assigned_posts' => $product->get_id(), 'rating' => 5]);
    createReview(['assigned_posts' => $product->get_id(), 'rating' => 5]);
    createReview(['assigned_posts' => $product->get_id(), 'rating' => 3]);

    $response = restRequest('GET', '/wc/v3/reports/reviews/totals');

    expect($response->get_status())->toBe(200);
    $totals = array_column($response->get_data(), 'total', 'slug');
    expect($totals['rated_5_out_of_5'])->toBe(2)
        ->and($totals['rated_3_out_of_5'])->toBe(1)
        ->and($totals['rated_1_out_of_5'])->toBe(0);
});

test('the store api serves the plugin\'s product reviews to a visitor', function () {
    $product = wooReviewedProduct();

    // The Store API declares product_id as a string (a comma-separated id list).
    $response = restRequest('GET', '/wc/store/v1/products/reviews', ['product_id' => (string) $product->get_id()]);

    expect($response->get_status())->toBe(200)
        ->and($response->get_headers()['X-WP-Total'])->toBe(1);
    $review = $response->get_data()[0];
    expect($review['product_id'])->toBe($product->get_id())
        ->and($review['rating'])->toBe(4)
        ->and($review['reviewer'])->toBe('Jane Doe')
        ->and($review['review'])->toBe("<p>Sturdy, arrived early.</p>\n")
        ->and($review['verified'])->toBeFalse()
        ->and($review['formatted_date_created'])->not->toBeEmpty();
});

test('a store api product carries the plugin\'s rating', function () {
    $product = wooReviewedProduct(5);

    $single = restRequest('GET', "/wc/store/v1/products/{$product->get_id()}");
    $collection = restRequest('GET', '/wc/store/v1/products', ['include' => [$product->get_id()]]);

    expect($single->get_status())->toBe(200)
        ->and($single->get_data()['average_rating'])->toBe('5')
        ->and($single->get_data()['review_count'])->toBe(1);
    expect($collection->get_status())->toBe(200)
        ->and($collection->get_data()[0]['id'])->toBe($product->get_id())
        ->and($collection->get_data()[0]['average_rating'])->toBe('5')
        ->and($collection->get_data()[0]['review_count'])->toBe(1);
});

test('the wc-analytics reviews route is rewired to the plugin', function () {
    wooActAsAdmin();
    $product = wooReviewedProduct();

    $response = restRequest('GET', '/wc-analytics/products/reviews');

    expect($response->get_status())->toBe(200);
    $review = $response->get_data()[0];
    expect($review['product_id'])->toBe($product->get_id())
        ->and($review['rating'])->toBe(4)
        ->and($review['reviewer'])->toBe('Jane Doe');
    // The collection folds each item's links into its data; the analytics namespace links up
    // to ITS product route, so the admin's data views stay inside wc-analytics.
    expect($review['_links']['up'][0]['href'])
        ->toEndWith("/wc-analytics/products/{$product->get_id()}");
});
