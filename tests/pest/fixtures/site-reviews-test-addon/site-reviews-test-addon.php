<?php
/**
 * Plugin Name: Site Reviews: Test Addon
 * Plugin URI:  https://niftyplugins.com/plugin/site-reviews-test-addon/
 * Update URI:  https://updates.example.org
 * Version:     2.3.4
 * Domain Path: /languages
 * Tested up to: 6.9
 * GLSR requires at least: 8.0.0
 * GLSR unsupported version: 99.0.0
 *
 * The two GLSR headers are what Application::register() version-gates on; a real addon carries
 * them, and the bounds are deliberately wide since this fixture tests what happens after the gate,
 * not the gate itself.
 *
 * This is a FIXTURE, not a plugin, exercising Addons\Controller, Addons\Hooks and the Updater
 * against a real addon rather than a mock — abstract classes whose job is to reach into an addon's
 * directory for its assets, config, views and languages, which a mock could only fake.
 *
 * Never loaded by WordPress. Plugin::__construct() derives $this->file by swapping
 * `plugin/Application` for the addon's id, so the LAYOUT matters: this file sits next to a plugin/
 * directory and is named after the id. The header above is read with get_file_data().
 */
