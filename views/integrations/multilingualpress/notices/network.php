<?php defined('ABSPATH') || exit; ?>

<div class="notice glsr-notice" data-notice="network">
    <p class="dashicons-before dashicons-admin-multisite">
        <?php printf(
            _x('Site Reviews is <a href="%s">network activated</a> so any settings you change will sync across your MultilingualPress sites.', 'admin-text', 'site-reviews'),
            network_admin_url('plugins.php')
        ); ?>
    </p>
</div>
