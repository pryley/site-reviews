<?php
/**
 * Plugin Name: Site Reviews: Premium Host
 * Plugin URI:  https://niftyplugins.com/plugin/site-reviews-premium-host/
 * Update URI:  https://updates.example.org
 * Version:     9.9.9
 * Domain Path: /languages
 * Tested up to: 6.9
 * GLSR requires at least: 8.0.0
 * GLSR unsupported version: 99.0.0
 *
 * A FIXTURE standing in for the merged premium plugin: a standalone-shaped
 * addon that registers OTHER addons with itself as $host. The hosted fixture
 * (site-reviews-hosted-addon) has no main file of its own — its version gates
 * come from THIS file, its paths resolve inside this directory, and its
 * settings are stored inside this fixture's option (site_reviews_premium_host)
 * while this fixture's own values occupy the top-level "features" key.
 *
 * Never loaded by WordPress; the tests require plugin/Application.php directly.
 */
