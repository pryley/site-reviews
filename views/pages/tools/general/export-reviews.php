<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="tools-export-reviews">
            <span class="title dashicons-before dashicons-admin-tools"><?php echo _x('Export Reviews', 'admin-text', 'site-reviews'); ?></span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="tools-export-reviews" class="inside">
        <div class="glsr-notice-inline components-notice is-info">
            <p class="components-notice__content">
                <?php echo sprintf(
                    _x('You can also use the WordPress %s and %s tools to export and import your reviews and categories.', 'admin-text', 'site-reviews'),
                    sprintf('<a href="%s">%s</a>', admin_url('export.php'), _x('Export', 'admin-text', 'site-reviews')),
                    sprintf('<a href="%s">%s</a>', admin_url('import.php'), _x('Import', 'admin-text', 'site-reviews'))
                ); ?>
            </p>
        </div>
        <p><?php echo sprintf(
            _x('Here you can export your reviews to a %s file. If you have assigned your reviews to pages and are planning to import them into a different website, you may need to export the Assigned Posts as %s since the Post IDs on the other website will likely be different.', 'admin-text', 'site-reviews'),
            '<code>*.csv</code>',
            '<code>post_type:slug</code>'
        ); ?></p>
        <form method="post">
            <?php wp_nonce_field('export-reviews'); ?>
            <input type="hidden" name="{{ id }}[_action]" value="export-reviews">
            <fieldset>
                <div data-col="assigned_terms">
                    <label for="export_assigned_terms"><strong><?php echo _x('Assigned Categories', 'admin-text', 'site-reviews'); ?></strong></label><br>
                    <select name="{{ id }}[assigned_terms]" id="export_assigned_terms">
                        <option value="id" selected><?php echo _x('Export as Term IDs', 'admin-text', 'site-reviews'); ?></option>
                        <option value="slug"><?php echo _x('Export as Term slugs', 'admin-text', 'site-reviews'); ?></option>
                    </select>
                </div>
                <div data-col="assigned_posts">
                    <label for="export_assigned_posts"><strong><?php echo _x('Assigned Posts', 'admin-text', 'site-reviews'); ?></strong></label><br>
                    <select name="{{ id }}[assigned_posts]" id="export_assigned_posts">
                        <option value="id" selected><?php echo _x('Export as Post IDs', 'admin-text', 'site-reviews'); ?></option>
                        <option value="slug"><?php echo sprintf(_x('Export as %s', 'post_type:slug (admin-text)', 'site-reviews'), 'post_type:slug'); ?></option>
                    </select>
                </div>
                <div data-col="assigned_users">
                    <label for="export_assigned_users"><strong><?php echo _x('Assigned Users', 'admin-text', 'site-reviews'); ?></strong></label><br>
                    <select name="{{ id }}[assigned_users]" id="export_assigned_users">
                        <option value="id" selected><?php echo _x('Export as User IDs', 'admin-text', 'site-reviews'); ?></option>
                        <option value="slug"><?php echo _x('Export as Usernames', 'admin-text', 'site-reviews'); ?></option>
                    </select>
                </div>
                <div data-col="date">
                    <label for="export_date"><strong><?php echo _x('Export Reviews After', 'admin-text', 'site-reviews'); ?></strong></label><br>
                    <input name="{{ id }}[date]" type="date" id="export_date">
                </div>
                <div data-col="post_status">
                    <label for="export_post_status"><strong><?php echo _x('Export Reviews With Status', 'admin-text', 'site-reviews'); ?></strong></label><br>
                    <select name="{{ id }}[post_status]" id="export_post_status">
                        <option value="" selected><?php echo _x('Approved and Unapproved reviews', 'admin-text', 'site-reviews'); ?></option>
                        <option value="publish"><?php echo _x('Approved reviews only', 'admin-text', 'site-reviews'); ?></option>
                        <option value="pending"><?php echo _x('Unapproved reviews only', 'admin-text', 'site-reviews'); ?></option>
                    </select>
                </div>
                <div data-col="author_id">
                    <label for="export_author"><strong><?php echo _x('Review Author', 'admin-text', 'site-reviews'); ?></strong></label><br>
                    <select name="{{ id }}[author_id]" id="export_author">
                        <option value="id" selected><?php echo _x('Export as User ID', 'admin-text', 'site-reviews'); ?></option>
                        <option value="slug"><?php echo _x('Export as Username', 'admin-text', 'site-reviews'); ?></option>
                    </select>
                </div>
            </fieldset>
            <div>
                <button type="submit" class="glsr-button button button-large button-primary">
                    <?php echo _x('Export Reviews', 'admin-text', 'site-reviews'); ?>
                </button>
            </div>
        </form>
    </div>
</div>
