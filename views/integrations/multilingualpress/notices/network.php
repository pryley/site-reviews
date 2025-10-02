<?php defined('ABSPATH') || exit; ?>

<p class="dashicons-before dashicons-admin-multisite">
    <?php printf(
        _x('Site Reviews is <a href="%s">network activated</a> so any settings you change will sync across your MultilingualPress sites.', 'admin-text', 'site-reviews'),
        network_admin_url('plugins.php')
    ); ?>
</p>
