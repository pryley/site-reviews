<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="tools-import-product-reviews">
            <span class="title dashicons-before dashicons-admin-tools"><?php echo _x('Import Product Reviews', 'admin-text', 'site-reviews'); ?></span>
            <span class="badge">WooCommerce</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="tools-import-product-reviews" class="inside">
        <div class="glsr-notice-inline components-notice is-info">
            <p class="components-notice__content">
                <?php echo _x("Reviews are only imported once so it's safe to run this tool multiple times.", 'admin-text', 'site-reviews'); ?>
            </p>
        </div>
        <div class="glsr-notice-inline components-notice is-warning">
            <p class="components-notice__content">
                <?php echo _x('If you decide to return to using WooCommerce reviews, you will need to click the "Revert" button to restore the previous product rating counts.', 'admin-text', 'site-reviews'); ?>
            </p>
        </div>

        <p>
            <strong><?php echo _x('Step 1:', 'admin-text', 'site-reviews'); ?></strong>
            <?php echo _x('Import your WooCommerce product reviews. Review replies and comments will be skipped.', 'admin-text', 'site-reviews'); ?>
        </p>
        <form method="post" enctype="multipart/form-data" onsubmit="submit.disabled = true;">
            <?php wp_nonce_field('import-product-reviews', '{{ id }}[_nonce]'); ?>
            <input type="hidden" name="{{ id }}[_action]" value="import-product-reviews">
            <div>
                <button type="submit" class="glsr-button button button-large button-primary"
                    data-ajax-import
                    data-loading="<?php echo esc_attr_x('Importing reviews, please wait...', 'admin-text', 'site-reviews'); ?>"
                ><?php echo _x('Import Reviews', 'admin-text', 'site-reviews'); ?>
                </button>
            </div>
        </form>

        <p>
            <strong><?php echo _x('Step 2:', 'admin-text', 'site-reviews'); ?></strong>
            <?php echo _x('Migrate the product rating counts. This allows third-party plugins to filter your products by rating.', 'admin-text', 'site-reviews'); ?>
        </p>
        <form method="post" enctype="multipart/form-data" onsubmit="submit.disabled = true;">
            <?php wp_nonce_field('migrate-product-ratings'); ?>
            <input type="hidden" name="{{ id }}[_action]" value="migrate-product-ratings">
            <input type="hidden" name="{{ id }}[alt]" value="0" data-alt>
            <button type="submit" class="glsr-button button button-large button-primary"
                data-ajax-click
                data-ajax-scroll
                data-loading="<?php echo esc_attr_x('Migrating, please wait...', 'admin-text', 'site-reviews'); ?>"
                data-remove-notice="migrate-product-ratings"
            ><?php echo _x('Migrate Product Ratings', 'admin-text', 'site-reviews'); ?>
            </button>
            <button type="submit" class="glsr-button button button-large button-secondary"
                data-ajax-click
                data-ajax-scroll
                data-alt
                data-loading="<?php echo esc_attr_x('Reverting, please wait...', 'admin-text', 'site-reviews'); ?>"
                data-remove-notice="migrate-product-ratings"
            ><?php echo _x('Revert', 'admin-text', 'site-reviews'); ?>
            </button>
        </form>

    </div>
</div>
