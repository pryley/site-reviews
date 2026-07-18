<?php
/**
 * Plugin Name: Site Reviews Premium
 * Plugin URI:  https://site-reviews.com/premium/
 * Version:     1.0.0
 * Domain Path: /languages
 * Tested up to: 6.9
 * GLSR requires at least: 8.0.0
 * GLSR unsupported version: 99.0.0
 *
 * A FIXTURE, not a plugin — the merged premium plugin's shell, reduced to what
 * License cares about: the `site-reviews-premium` id and `LICENSED = true`.
 * Registering it is what makes glsr()->addon('site-reviews-premium') answer,
 * which is the whole of License's "premium is installed" test.
 *
 * Never loaded by WordPress. Plugin::__construct() derives $this->file by
 * swapping `plugin/Application` for the addon's id, so the LAYOUT matters:
 * this file sits next to a plugin/ directory and is named after the id.
 */
