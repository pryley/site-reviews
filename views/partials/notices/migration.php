<?php defined('ABSPATH') || exit; ?>

<div class="notice is-dismissible glsr-notice" data-notice="migration">
    <p>
        <?php printf(_x('Site Reviews needs to migrate your reviews and settings to the latest version. If this notice continues to appear after 5 minutes, please read the %s section on the Help page.', 'admin-text', 'site-reviews'),
            sprintf('<a href="%s" data-expand="#support-common-problems-and-solutions">%s</a>',
                glsr_admin_url('documentation', 'support'),
                _x('Common Problems and Solutions', 'admin-text', 'site-reviews')
            )
        ); ?>
    </p>
    <?php if (glsr()->hasPermission('tools', 'general')) { ?>
        <form method="post">
            <?php wp_nonce_field('migrate-plugin'); ?>
            <input type="hidden" name="<?php echo glsr()->id; ?>[_action]" value="migrate-plugin">
            <p class="glsr-notice-buttons">
                <button type="submit" class="glsr-button button button-primary"
                    data-ajax-click
                    data-remove-notice="migration"
                    data-loading="<?php echo esc_attr_x('Migrating, please wait...', 'admin-text', 'site-reviews'); ?>"
                ><?php echo _x('Run Migration', 'admin-text', 'site-reviews'); ?>
                </button>
            </p>
        </form>
    <?php } ?>
</div>
