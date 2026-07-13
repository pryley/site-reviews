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
 * The two GLSR headers are what Application::register() version-gates on, and a real addon
 * always carries them. The bounds are deliberately wide: this fixture is not testing the gate,
 * it is testing everything that happens after it, and a fixture that fell out of range every
 * time the plugin's version moved would be a fixture that broke on release day.
 *
 * This is a FIXTURE, not a plugin. It exists so that Addons\Controller, Addons\Hooks and
 * the Updater can be tested against a real addon rather than a mock of one -- they are
 * abstract classes whose whole job is to reach into an addon's directory for its assets,
 * its config, its views and its languages, and a mock would only prove that the mock
 * agreed with itself.
 *
 * It is never loaded by WordPress. Plugin::__construct() derives $this->file by swapping
 * `plugin/Application` for the addon's id, so the LAYOUT is what matters: this file has to
 * sit next to a plugin/ directory and be named after the id. The header above is read with
 * get_file_data(), which is where name, version, languages and testedTo come from.
 */
