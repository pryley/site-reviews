<div class="notice notice-warning is-dismissible glsr-notice" data-notice="migrate">
    <form method="post">
        <input type="hidden" name="<?= glsr()->id; ?>[_action]" value="migrate-plugin">
        <input type="hidden" name="<?= glsr()->id; ?>[alt]" value="0" data-alt>
        <?php wp_nonce_field('migrate-plugin'); ?>
        <p><strong><?= _x('Database Update Required', 'admin-text', 'site-reviews'); ?></strong></p>
        <p><?= sprintf(_x('Site Reviews needs to update the database and your reviews to the newest version. If this notice keeps appearing, please read the %s section on the Help page.', 'admin-text', 'site-reviews'), $action); ?></p>
        <p>
            <button type="submit" class="glsr-button components-button is-secondary" data-ajax-click data-remove-notice="migrate">
                <span data-alt-text="<?= esc_attr_x('Run All Migrations', 'admin-text', 'site-reviews'); ?>" data-loading="<?= esc_attr_x('Updating, please wait...', 'admin-text', 'site-reviews'); ?>"><?= _x('Update Database', 'admin-text', 'site-reviews'); ?></span>
            </button>
        </p>
    </form>
</div>
