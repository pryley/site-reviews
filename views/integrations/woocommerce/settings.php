<?php defined('WPINC') || exit; ?>

<h2 class="title">WooCommerce</h2>

<div class="components-notice is-info" style="margin-left:0;">
    <p class="components-notice__content">
    <?php printf(_x('Import your existing WooCommerce product reviews %shere%s.', 'admin-text', 'site-reviews'),
        sprintf('<a data-expand="#tools-import-product-reviews" href="%s">', glsr_admin_url('tools', 'general')),
        '</a>'
    ); ?>
    </p>
</div>

<table class="form-table">
    <tbody>
        {{ rows }}
    </tbody>
</table>
