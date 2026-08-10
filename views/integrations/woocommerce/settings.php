<?php defined('WPINC') || exit; ?>

<h2 class="title">WooCommerce</h2>

<?php /* translators: %s: link with the text "Import" */ echo wp_get_admin_notice(
    sprintf(_x('%s your existing WooCommerce product reviews.', 'Import (admin-text)', 'site-reviews'),
        glsr_admin_link('tools.general', _x('Import', 'admin-text', 'site-reviews'), '#tools-import-product-reviews')
    ),
    ['type' => 'info', 'additional_classes' => ['inline']]
); ?>

<table class="form-table">
    <tbody>
        {{ rows }}
    </tbody>
</table>
