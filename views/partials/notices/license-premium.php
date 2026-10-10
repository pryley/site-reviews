<?php defined('ABSPATH') || exit; ?>

<?php
    printf(
        /* translators: %s: link with the text "Install Site Reviews Premium" */
        _x('Your license includes Site Reviews Premium. %s to replace your addons with it.', 'Install Site Reviews Premium (admin-text)', 'site-reviews'),
        sprintf('<a href="%s" data-glsr-premium-install>%s</a>', esc_url(glsr_admin_url('settings', 'general')), _x('Install Site Reviews Premium', 'admin-text', 'site-reviews'))
    );
?>
<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php echo _x('Dismiss this notice.', 'admin-text', 'site-reviews'); ?></span></button>
