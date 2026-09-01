<?php

/*
 * Per-test isolation for the four main suites: a DB transaction that rolls
 * back, hook and request resets, and the tripwires for a committed transaction
 * and a leaked WP_IMPORTING. The closures live in Support/isolation.php, shared
 * with the WooCommerce suite (tests/woocommerce/Pest.php), which runs the same
 * discipline against a real WooCommerce in its own wp-env instance.
 */

$isolation = require __DIR__.'/Support/isolation.php';

uses()
    ->beforeEach($isolation['beforeEach'])
    ->afterEach($isolation['afterEach'])
    ->in('Unit', 'Integration', 'ThirdParty', 'Import');
