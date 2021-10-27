<?php defined('ABSPATH') || die;

/**
 * This hack allow the list table to set the correct URLs
 */
$originalRequestUri = $_SERVER['REQUEST_URI'];
$_SERVER['REQUEST_URI'] = parse_url(admin_url('edit.php'), PHP_URL_PATH).'?post_type=site-review&page=tools&tab=scheduled';
/**
 * Display the list table
 */
$table = glsr('Overrides\ScheduledActionsTable');
$table->process_actions();
$table->display_page();
/**
 * Finally, restore the original REQUEST_URI
 */
$_SERVER['REQUEST_URI'] = $originalRequestUri;
