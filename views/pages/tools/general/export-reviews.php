<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="tools-export-reviews">
            <span class="title dashicons-before dashicons-admin-tools"><?= _x('Export Reviews', 'admin-text', 'site-reviews'); ?></span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="tools-export-reviews" class="inside">
        <div class="glsr-notice-inline components-notice is-warning">
            <p class="components-notice__content">
                <?= _x('This tool does not yet support custom fields!', 'admin-text', 'site-reviews'); ?>
            </p>
        </div>
        <div class="glsr-notice-inline components-notice is-info">
            <p class="components-notice__content">
                <?= sprintf(
                    _x('You can also use the WordPress %s and %s tools to export and import your reviews and categories.', 'admin-text', 'site-reviews'),
                    sprintf('<a href="%s">%s</a>', admin_url('export.php'), _x('Export', 'admin-text', 'site-reviews')),
                    sprintf('<a href="%s">%s</a>', admin_url('import.php'), _x('Import', 'admin-text', 'site-reviews'))
                ); ?>
            </p>
        </div>
        <p><?= sprintf(
            _x('Here you can export your reviews to a %s file. If you have assigned your reviews to pages and are planning to import them into a different website, you may need to export the Assigned Posts as %s since the Post IDs on the other website will likely be different.', 'admin-text', 'site-reviews'),
            '<code>*.csv</code>',
            '<code>post_type:slug</code>'
        ); ?></p>
        <form method="post">
            <?php wp_nonce_field('export-reviews'); ?>
            <input type="hidden" name="{{ id }}[_action]" value="export-reviews">
            <p>
                <label for="export_assigned_posts"><strong><?= _x('Export Assigned Posts As', 'admin-text', 'site-reviews'); ?></strong></label><br>
                <select name="{{ id }}[assigned_posts]" id="export_assigned_posts">
                    <option value="id" selected><?= _x('Export as Post IDs', 'admin-text', 'site-reviews'); ?></option>
                    <option value="slug"><?= sprintf(_x('Export as %s', 'post_type:slug (admin-text)', 'site-reviews'), 'post_type:slug'); ?></option>
                </select>
            </p>
            <p>
                <label for="export_date"><strong><?= _x('Export Reviews Submitted After', 'admin-text', 'site-reviews'); ?></strong></label><br>
                <input name="{{ id }}[date]" type="datetime-local" id="export_date">
            </p>
            <p>
                <label for="export_post_status"><strong><?= _x('Export Reviews With Status', 'admin-text', 'site-reviews'); ?></strong></label><br>
                <select name="{{ id }}[post_status]" id="export_post_status">
                    <option value="" selected><?= _x('Approved and Unapproved reviews', 'admin-text', 'site-reviews'); ?></option>
                    <option value="publish"><?= _x('Approved reviews only', 'admin-text', 'site-reviews'); ?></option>
                    <option value="pending"><?= _x('Unapproved reviews only', 'admin-text', 'site-reviews'); ?></option>
                </select>
            </p>
            <button type="submit" class="components-button is-primary">
                <?= _x('Export Reviews', 'admin-text', 'site-reviews'); ?>
            </button>
        </form>
    </div>
</div>
