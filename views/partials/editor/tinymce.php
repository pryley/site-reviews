<?php defined('ABSPATH') || exit; ?>

<div class="glsr-mce">
    <button class="button glsr-mce-button">
        <span class="wp-media-buttons-icon"></span>
    </button>
    <div class="mce-menu glsr-mce-menu">
    <?php foreach ($shortcodes as $key => $values) { ?>
        <div class="mce-menu-item glsr-mce-menu-item" data-shortcode="<?php echo esc_attr($key); ?>"><?php echo esc_html($values['label']); ?></div>
    <?php } ?>
    </div>
</div>
