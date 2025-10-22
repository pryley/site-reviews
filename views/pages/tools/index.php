<?php defined('ABSPATH') || exit; ?>

<div class="wrap">
    <hr class="wp-header-end" />
    <?php echo $notices; ?>
    <nav class="glsr-nav-tab-wrapper nav-tab-wrapper">
        <?php foreach ($tabs as $id => $title) : ?>
        <a class="glsr-nav-tab nav-tab" data-id="<?php echo esc_attr($id); ?>" href="<?php echo esc_url(glsr_admin_url('tools', $id)); ?>" tabindex="0"><?php echo esc_html($title); ?></a>
        <?php endforeach; ?>
    </nav>
    <?php foreach ($tabs as $id => $title) : ?>
    <div class="glsr-nav-view ui-tabs-hide" id="<?php echo esc_attr($id); ?>">
        <?php glsr('Modules\Html\Template')->render("pages/tools/{$id}", $data); ?>
    </div>
    <?php endforeach; ?>
    <input type="hidden" name="_active_tab">
    <input type="hidden" name="_wp_http_referer" value="<?php echo esc_attr($http_referer); ?>">
</div>
