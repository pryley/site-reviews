<?php

/*
 * Appended to the generated stub by tests/bin/generate-stubs.php — symbols the
 * generator cannot reach in the myCRED source:
 *
 * - myCRED_Hook_WooCommerce_Reviews is declared INSIDE mycred_load_woocommerce_hook()
 *   (includes/hooks/external/mycred-hook-woocommerce.php), so no stub generator
 *   ever sees it. MyCredHookWooReviews extends it, and the integration's
 *   Controller gates on class_exists().
 * - MYCRED_DEFAULT_TYPE_KEY is defined via $this->define() in myCRED_Core, not a
 *   bare define(). The integration's isInstalled() gates on it.
 *
 * Namespaces must stay braced: this is concatenated onto a file of braced
 * namespace blocks.
 */
namespace {
    class myCRED_Hook_WooCommerce_Reviews extends \myCRED_Hook
    {
    }
    \define('MYCRED_DEFAULT_TYPE_KEY', 'mycred_default');
}
