<?php defined('ABSPATH') || exit; ?>

<div class="notice notice-info is-dismissible glsr-notice">
    <p>
    <?php
        printf(_x('Your Product reviews are being managed by Site Reviews because you enabled the %s in the settings.', 'WooCommerce integration (admin-text)', 'site-reviews'),
            glsr_admin_link('settings.integrations.woocommerce', _x('WooCommerce integration', 'admin-text', 'site-reviews'))
        );
    ?>
    </p>
    <p>
        <a class="button button-secondary" href="<?php echo glsr_admin_url('tools'); ?>"><?php echo esc_html_x('Import your WooCommerce reviews', 'admin-text', 'site-reviews'); ?></a>
    </p>
</div>
