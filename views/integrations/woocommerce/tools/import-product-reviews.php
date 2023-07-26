<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="tools-import-product-reviews">
            <span class="title dashicons-before dashicons-admin-tools"><?= _x('Import Product Reviews', 'admin-text', 'site-reviews'); ?></span>
            <span class="badge">WooCommerce</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="tools-import-product-reviews" class="inside">
        <div class="glsr-notice-inline components-notice is-info">
            <p class="components-notice__content">
                <?= _x("Reviews are only imported once so it's safe to run this tool multiple times.", 'admin-text', 'site-reviews'); ?>
            </p>
        </div>
        <p>
            <?= _x('This tool will import your WooCommerce Product reviews. Review replies and comments will be skipped.', 'admin-text', 'site-reviews'); ?>
        </p>
        <form method="post" enctype="multipart/form-data" onsubmit="submit.disabled = true;">
            <?php wp_nonce_field('import-product-reviews', '{{ id }}[_nonce]'); ?>
            <input type="hidden" name="{{ id }}[_action]" value="import-product-reviews">
            <button type="submit" class="glsr-button components-button is-primary"
                data-ajax-import
                data-remove-notice="import-product-reviews"
                data-loading="<?= esc_attr_x('Importing reviews, please wait...', 'admin-text', 'site-reviews'); ?>"
            ><?= _x('Import Reviews', 'admin-text', 'site-reviews'); ?>
            </button>
        </form>
    </div>
</div>
