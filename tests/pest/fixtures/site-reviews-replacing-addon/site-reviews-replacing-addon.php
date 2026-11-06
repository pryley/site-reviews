<?php
/**
 * Plugin Name: Site Reviews: Replacing Addon
 * Version:     1.0.0
 * GLSR requires at least: 8.0.0
 * GLSR unsupported version: 99.0.0
 *
 * A FIXTURE for the REPLACES constant: a standalone-shaped addon that declares it replaces the
 * plain addon and the test addon, so registering it beside either shelves that one. The premium
 * host fixture names THIS id in turn, which is how a shelved addon's own list is shown to count
 * for nothing. See ApplicationTest.
 *
 * Never loaded by WordPress. Plugin::__construct() derives $this->file by swapping
 * `plugin/Application` for the addon's id, so the LAYOUT matters: this file sits next to a plugin/
 * directory and is named after the id.
 */
