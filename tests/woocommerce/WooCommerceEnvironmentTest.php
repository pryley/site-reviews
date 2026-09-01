<?php

use GeminiLabs\SiteReviews\Integrations\WooCommerce\Controllers\ProductController;

use function GeminiLabs\SiteReviews\Tests\enableWooCommerceIntegration;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;
use function GeminiLabs\SiteReviews\Tests\wooCompletedOrder;
use function GeminiLabs\SiteReviews\Tests\wooProduct;
use function GeminiLabs\SiteReviews\Tests\wooResetProductData;
use function GeminiLabs\SiteReviews\Tests\wooSetupProductData;

/*
 * The environment itself, checked before anything is built on it: a real
 * WooCommerce (not its stub), the integration bound at plugin load, and
 * WooCommerce's own writes — products, and orders in whichever table this
 * WooCommerce keeps them (HPOS is off in the wp-env instance) — staying
 * inside the per-test transaction.
 *
 * The commit tripwire in tests/pest/Support/isolation.php is the assertion for
 * that last one: an order write that opened its own transaction would COMMIT,
 * the sentinel row would survive the rollback, and the test would fail by name.
 */

beforeEach(function () {
    resetPluginState();
    enableWooCommerceIntegration();
});

test('the real woocommerce is loaded, not its stub', function () {
    expect(defined('WC_VERSION'))->toBeTrue()
        ->and(WC()->version)->toBe(WC_VERSION)
        ->and(wc_get_product(wooProduct()->get_id()))->toBeInstanceOf(\WC_Product_Simple::class);
});

test('the integration bound its hooks when the plugin loaded', function () {
    wooSetupProductData(wooProduct());
    try {
        $tabs = apply_filters('woocommerce_product_tabs', []);
    } finally {
        wooResetProductData();
    }

    expect($tabs['reviews']['callback'][0])->toBeInstanceOf(ProductController::class)
        ->and($tabs['reviews']['callback'][1])->toBe('renderSingleProductReviews');
});

test('creating a product stays inside the test transaction', function () {
    expect(wooProduct()->get_id())->toBeGreaterThan(0);
});

test('creating a completed order stays inside the test transaction', function () {
    $order = wooCompletedOrder(wooProduct(), 'jane@example.org');

    expect($order->get_id())->toBeGreaterThan(0)
        ->and(wc_get_order($order->get_id())->get_status())->toBe('completed');
});
