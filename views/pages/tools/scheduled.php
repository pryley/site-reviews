<?php defined('ABSPATH') || exit; ?>

<div class="components-notice is-info" style="margin:20px 0 2px;">
    <p class="components-notice__content">
        <?php echo _x('Site Reviews includes and uses the versatile <a href="https://actionscheduler.org" target="_blank">Action Scheduler</a> library to manage and schedule cron jobs in WordPress and improve your site’s overall ability to process large tasks. Many other popular WordPress plugins like WooCommerce, RankMath, WPForms, and All in One SEO also rely on Action Scheduler to execute actions more efficiently.', 'admin-text', 'site-reviews'); ?>
    </p>
</div>

<?php glsr('Overrides\ScheduledActionsTable')->display_page();
