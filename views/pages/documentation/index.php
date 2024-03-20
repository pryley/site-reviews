<?php defined('ABSPATH') || exit; ?>

<div class="wrap">
    <hr class="wp-header-end" />
    <?php echo $notices; ?>
    <p><?php echo _x('Click an active tab to expand/collapse all sections.', 'admin-text', 'site-reviews'); ?></p>
    <nav class="glsr-nav-tab-wrapper nav-tab-wrapper">
        <?php foreach ($tabs as $id => $title) { ?>
        <a class="glsr-nav-tab nav-tab" data-id="<?php echo esc_attr($id); ?>" href="<?php echo esc_url(glsr_admin_url('documentation', $id)); ?>" tabindex="0"><?php echo $title; ?></a>
        <?php } ?>
    </nav>
    <?php foreach ($tabs as $id => $title) { ?>
    <div class="glsr-nav-view ui-tabs-hide" id="<?php echo esc_attr($id); ?>">
        <?php glsr()->render("pages/documentation/{$id}", $data); ?>
    </div>
    <?php } ?>
    <input type="hidden" name="_active_tab">
    <input type="hidden" name="_wp_http_referer" value="<?php echo $http_referer; ?>">
</div>
