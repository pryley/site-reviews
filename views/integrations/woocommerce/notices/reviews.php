<?php defined('ABSPATH') || exit; ?>

<div class="notice notice-info is-dismissible">
    <p>
    <?php
        printf(_x('Your product reviews are being managed by <strong>Site Reviews</strong> and can be found on the %sAll Reviews%s page.', 'admin-text', 'site-reviews'),
            sprintf('<a href="%s">', glsr_admin_url()), '</a>'
        );
    ?>
    </p>
    <p>
        <a class="button button-primary" href="<?php echo glsr_admin_url('tools'); ?>"><?php echo esc_html_x('Import your WooCommerce product reviews', 'admin-text', 'site-reviews'); ?></a>
    </p>
</div>
