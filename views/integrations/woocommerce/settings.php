<?php defined('WPINC') || exit; ?>

<h2 class="title">WooCommerce</h2>

<div class="components-notice is-info" style="margin-left:0;">
    <p class="components-notice__content">
    <?php printf(_x('%s your existing WooCommerce product reviews.', 'Import (admin-text)', 'site-reviews'),
        glsr_admin_link('tools.general', _x('Import', 'admin-text', 'site-reviews'), '#tools-import-product-reviews')
    ); ?>
    </p>
</div>

<table class="form-table">
    <tbody>
        {{ rows }}
    </tbody>
</table>
