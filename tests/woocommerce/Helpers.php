<?php

namespace GeminiLabs\SiteReviews\Tests;

use GeminiLabs\SiteReviews\Database\OptionManager;

/*
 * Helpers shared by the WooCommerce suite's files, required by its Pest.php.
 * They live here rather than in tests/pest/Support because only this suite has
 * a real WooCommerce to call.
 */

/**
 * resetPluginState() restores the defaults, where the integration is off. The hooks
 * were bound at plugin load (Hooks::runIfEnabled), so this only matters to the code
 * that reads the setting at run time — the product page's shortcodes, for one.
 */
function enableWooCommerceIntegration(): void
{
    glsr(OptionManager::class)->set('settings.integrations.woocommerce.enabled', 'yes');
}

/**
 * A completed order for $product placed by $email. A completed order is what makes
 * wc_customer_bought_product() answer true; the customer id is set too, so the
 * check answers by user as well as by email.
 */
function wooCompletedOrder(\WC_Product $product, string $email, int $customerId = 0): \WC_Order
{
    $order = wc_create_order();
    $order->add_product($product, 1);
    $order->set_billing_email($email);
    $order->set_customer_id($customerId);
    $order->set_status('completed');
    $order->save();
    return $order;
}

/**
 * A published simple product, saved through WooCommerce's own CRUD so its meta and
 * lookup-table rows are what a real store has.
 */
function wooProduct(array $props = []): \WC_Product_Simple
{
    $product = new \WC_Product_Simple();
    $product->set_name($props['name'] ?? 'Test Product');
    $product->set_description($props['description'] ?? '');
    $product->set_regular_price($props['price'] ?? '10');
    $product->set_status('publish');
    $product->save();
    return $product;
}

/**
 * Puts $product where WooCommerce's template functions look for it: the $post and
 * $product globals, as wc_setup_product_data() sets them on a product page. Undo with
 * wooResetProductData(); resetGlobalState() drops $post but knows nothing of $product.
 */
function wooSetupProductData(\WC_Product $product): void
{
    $GLOBALS['post'] = get_post($product->get_id());
    setup_postdata($GLOBALS['post']);
    $GLOBALS['product'] = wc_get_product($product->get_id());
}

function wooResetProductData(): void
{
    wp_reset_postdata();
    unset($GLOBALS['post'], $GLOBALS['product']);
}
