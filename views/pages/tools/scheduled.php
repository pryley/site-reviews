<?php defined('ABSPATH') || exit; ?>

<div class="components-notice is-info" style="margin:20px 0 2px;">
    <p class="components-notice__content">
        <?= _x('Site Reviews uses a versatile library called <a href="https://actionscheduler.org" target="_blank">Action Scheduler</a> to manage and schedule cron jobs in WordPress. Action Scheduler improves your site’s overall ability to process large tasks. Many other popular WordPress plugins like WooCommerce, RankMath, WPForms, and All in One SEO also rely on Action Scheduler to help execute actions more efficiently.', 'admin-text', 'site-reviews'); ?>
    </p>
</div>

<?php

/**
 * This hack allow the list table to set the correct URLs
 */
$originalRequestUri = $_SERVER['REQUEST_URI'];
$_SERVER['REQUEST_URI'] = parse_url(admin_url('edit.php'), PHP_URL_PATH).'?post_type=site-review&page='.str_replace('_', '-', glsr()->prefix).'tools&tab=scheduled';
/**
 * Display the list table
 */
glsr('Overrides\ScheduledActionsTable')->display_page();
/**
 * Finally, restore the original REQUEST_URI
 */
$_SERVER['REQUEST_URI'] = $originalRequestUri;
