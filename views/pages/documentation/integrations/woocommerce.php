<?php defined('ABSPATH') || exit; ?>
<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="integrations-woocommerce">
            <span class="title has-logo">
                <?php 
                    echo \GeminiLabs\SiteReviews\Helpers\Svg::get('assets/images/icons/integrations/woocommerce.svg', [
                        'fill' => 'currentColor',
                        'height' => 24,
                        'width' => 24,
                    ]);
                ?>
                WooCommerce
            </span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="integrations-woocommerce" class="inside">
        <h3>Enable the WooCommerce integration</h3>
        <p>Go to the <?php echo glsr_admin_link('settings.integrations.woocommerce'); ?> page and enable the integration.</p>
        <p>After the integration is enabled, Site Reviews will automatically be added to the Reviews tab of your Product pages (replacing the old WooCommerce reviews section) and all WooCommerce API calls will return rating and reviews data from Site Reviews.</p>
        <h3>Import your existing Product reviews</h3>
        <p>Use the <?php echo glsr_admin_link('tools.general', _x('Import Product Reviews', 'admin-text', 'site-reviews'), '#tools-import-product-reviews'); ?> tool to import your existing Woocommerce Product reviews into Site Reviews.</p>
    </div>
</div>
