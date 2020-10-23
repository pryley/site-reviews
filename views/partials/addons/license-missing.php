<?php defined('ABSPATH') || die; ?>

<strong><a href="<?= esc_url(admin_url('edit.php?post_type='.glsr()->post_type.'&page=settings#tab-licenses')); ?>">
    <?= _x('Enter a valid license key for automatic updates.', 'admin-text', 'site-reviews'); ?>
</a></strong>
