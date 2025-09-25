<?php defined('WPINC') || exit; ?>

<div class="hook-instance">
    <div class="notice notice-alt inline notice-warning" style="margin-left:0;">
        <p class="dashicons-before dashicons-warning">
            <?php printf(
                esc_html_x('This hook is disabled because the %s is enabled. Please use the Site Reviews hook instead or disable the integration.', 'link to WooCommerce integration setting (admin-text)', 'site-reviews'),
                glsr_admin_link('settings.integrations.woocommerce', _x('WooCommerce integration', 'admin-text', 'site-reviews'))
            ); ?>
        </p>
    </div>
</div>
