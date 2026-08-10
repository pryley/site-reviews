<?php defined('ABSPATH') || exit; ?>

<?php echo wp_get_admin_notice(
    _x('Site Reviews includes and uses the versatile <a href="https://actionscheduler.org" target="_blank">Action Scheduler</a> library to manage and schedule cron jobs in WordPress and improve your site’s overall ability to process large tasks. Many other popular WordPress plugins like WooCommerce, RankMath, WPForms, and All in One SEO also rely on Action Scheduler to execute actions more efficiently.', 'admin-text', 'site-reviews'),
    ['type' => 'info', 'additional_classes' => ['inline']]
); ?>

<?php glsr('Overrides\ScheduledActionsTable')->display_page();
