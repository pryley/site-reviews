<?php defined('ABSPATH') || exit; ?>

<div class="wrap">
    <h1><?php echo $title; ?></h1>
    <iframe src="<?php echo esc_url($url); ?>" style="width: 100%; height: 100%; min-height: 750px;" frameborder="0" title="<?php echo esc_attr_x('Update progress', 'admin-text', 'site-reviews'); ?>"></iframe>
</div>
