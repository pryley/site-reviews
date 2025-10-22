<?php defined('ABSPATH') || exit; ?>

<?php
    printf(
        _x('One or more addons are not receiving updates. %s in the settings to enable plugin updates and support.', 'Save your license (admin-text)', 'site-reviews'),
        glsr_admin_link('settings.licenses', _x('Save your license', 'admin-text', 'site-reviews'))
    );
?>
<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php echo _x('Dismiss this notice.', 'admin-text', 'site-reviews'); ?></span></button>
