<?php

/*
 * Boots the WOOCOMMERCE wp-env instance (tests/woocommerce/.wp-env.json — its
 * own containers on port 8894, a real WooCommerce installed, the integration
 * enabled by `make test:woocommerce`). This suite exists for the half of the
 * WooCommerce integration the main suite structurally cannot reach: the paths
 * that consume a WooCommerce RETURN VALUE — wc_get_product(), the wc/v3 and
 * Store API controllers, wc_customer_bought_product() — which a signature-only
 * stub answers with nothing.
 *
 * Everything else is the main suite's bootstrap, required as-is: the same
 * constants, the same mu-plugin (which drops the WooCommerce stub because the
 * real plugin is active, and still loads every other stub), the same per-test
 * transaction (Pest.php). What a transaction cannot isolate is the same here
 * as there; see tests/pest/Support/isolation.php.
 */

/*
 * Integrations whose own plugin is a stub here but which act on WooCommerce
 * products: with a real product in hand they proceed into their stub, which
 * answers null, and die. Half real and half nothing proves nothing about
 * them, so their stubs are not loaded in this instance (the mu-plugin reads
 * this list). The main suite still covers the half of each that it can.
 */
define('GLSR_TEST_EXCLUDED_STUBS', [
    'gamipress.php',
    'lpfw.php',
    'mycred.php',
    'wlpr.php',
    'woorewards.php',
    'wp-loyalty-rules.php',
]);

require __DIR__.'/../pest/bootstrap.php';

// The stub declares WooCommerce's classes but none of its constants: WC_VERSION
// exists only when the real plugin loaded.
if (!defined('WC_VERSION')) {
    fwrite(STDOUT, implode(PHP_EOL, [
        'WooCommerce is not installed in this WordPress (the stub answered instead).',
        'Run this suite with `make test:woocommerce`, which starts the WooCommerce',
        'wp-env instance (tests/woocommerce/.wp-env.json).',
        '',
    ]));
    exit(1);
}

// Hooks::runIfEnabled() binds the integration when the plugin LOADS, from the
// stored setting — too early for a test to flip it. `make test:woocommerce`
// stores it; a fresh instance that lost it says so instead of running every
// test against an integration that is not there.
if ('yes' !== glsr_get_option('integrations.woocommerce.enabled')
    || 'yes' !== get_option('woocommerce_enable_reviews', 'yes')) {
    fwrite(STDOUT, implode(PHP_EOL, [
        'The WooCommerce integration is not enabled in this WordPress, so its hooks',
        'were not bound when the plugin loaded. Run:',
        '    make test:woocommerce',
        'which enables it before running the suite.',
        '',
    ]));
    exit(1);
}
