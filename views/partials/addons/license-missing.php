<?php defined('WPINC') || die; ?>

<strong><a href="<?= esc_url(admin_url('edit.php?post_type='.glsr()->post_type.'&page=settings#tab-licenses')); ?>">
    <?= __('Enter a valid license key for automatic updates.', 'site-reviews'); ?>
</a></strong>
