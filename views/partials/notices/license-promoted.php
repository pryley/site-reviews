<?php defined('ABSPATH') || exit; ?>

<?php
    printf(
        _x('You are using the free version of Site Reviews. %s to support future development and get images, filters, themes, custom forms, and more!', 'Purchase premium (admin-text)', 'site-reviews'),
        glsr_premium_link('site-reviews-premium', _x('Purchase premium', 'admin-text', 'site-reviews'))
    );
?>
<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php echo _x('Dismiss this notice.', 'admin-text', 'site-reviews'); ?></span></button>
