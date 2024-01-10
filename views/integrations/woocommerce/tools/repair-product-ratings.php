<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="tools-repair-product-ratings">
            <span class="title dashicons-before dashicons-admin-tools"><?= _x('Repair Product Ratings', 'admin-text', 'site-reviews'); ?></span>
            <span class="badge">WooCommerce</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="tools-repair-product-ratings" class="inside">
        <p>
            <?= _x('This tool will sync the average rating of products to the terms in the WooCommerce <code>product_visibility</code> taxonomy. This will fix compatibility with third-party filter plugins.', 'admin-text', 'site-reviews'); ?>
        </p>
        <form method="post" enctype="multipart/form-data" onsubmit="submit.disabled = true;">
            <?php wp_nonce_field('repair-product-ratings', '{{ id }}[_nonce]'); ?>
            <input type="hidden" name="{{ id }}[_action]" value="repair-product-ratings">
            <button type="submit" class="glsr-button components-button is-primary"
                data-ajax-import
                data-remove-notice="repair-product-ratings"
                data-loading="<?= esc_attr_x('Repairing product ratings, please wait...', 'admin-text', 'site-reviews'); ?>"
            ><?= _x('Repair product ratings', 'admin-text', 'site-reviews'); ?>
            </button>
        </form>
    </div>
</div>
