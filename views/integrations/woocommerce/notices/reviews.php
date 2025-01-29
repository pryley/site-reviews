<?php defined('ABSPATH') || exit; ?>

<div class="notice notice-info is-dismissible glsr-notice">
    <p>
    <?php
        printf(_x('Your Product reviews are being managed by Site Reviews because you enabled the %sWooCommerce integration%s in the settings.', 'admin-text', 'site-reviews'),
            sprintf('<a href="%s">', glsr_admin_url('settings', 'integrations', 'woocommerce')), '</a>'
        );
    ?>
    </p>
    <p>
        <a class="button button-secondary" href="<?php echo glsr_admin_url('tools'); ?>"><?php echo esc_html_x('Import your WooCommerce reviews', 'admin-text', 'site-reviews'); ?></a>
    </p>
</div>
