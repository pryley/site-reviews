<?php defined('WPINC') || exit; ?>

<div class="hook-instance">
    <div class="notice notice-alt inline notice-warning" style="margin-left:0;">
        <p class="dashicons-before dashicons-warning">
            <?php printf(
                esc_html_x('This hook is disabled because the %s integration is enabled. Please use the Site Reviews hook instead or disable the integration.', 'link to WooCommerce integration setting (admin-text)', 'site-reviews'),
                sprintf('<a href="%s">%s</a>',
                    glsr_admin_url('settings', 'integrations', 'woocommerce'),
                    esc_html_x('Site Reviews WooCommerce', 'admin-text', 'site-reviews')
                )
            ); ?>
        </p>
    </div>
</div>
