<?php defined('ABSPATH') || exit; ?>

<div class="wrap">
    <hr class="wp-header-end" />
    <?php echo $notices; ?>
    <nav class="glsr-nav-tab-wrapper nav-tab-wrapper">
        <?php foreach ($tabs as $id => $title) { ?>
        <a class="glsr-nav-tab nav-tab" data-id="<?php echo esc_attr($id); ?>" href="<?php echo glsr_admin_url('settings', $id); ?>" tabindex="0"><?php echo $title; ?></a>
        <?php } ?>
    </nav>
    <form class="glsr-form" action="options.php" enctype="multipart/form-data" method="post">
        <input type="hidden" name="_active_tab" />
        <?php settings_fields(glsr()->id); ?>
        <?php echo $fields; ?>
        <?php submit_button(); ?>
    </form>
</div>
