<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox is-fullwidth">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="welcome-v6_4_0">
            <span class="title">Version 6.4</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="welcome-v6_4_0" class="inside">
        <p><em>Release Date &mdash; December 27th, 2022</em></p>

        <h4>✨ New Features</h4>
        <ul>
            <li>Added Action Scheduler information to System Info</li>
            <li>Added assignment meta keys (<code>_glsr_average</code>, <code>_glsr_ranking</code>, <code>_glsr_reviews</code>) to the WP REST API</li>
            <li>Added compatibility with <a href="https://advancedcouponsplugin.com/woocommerce-loyalty-program/" target="_blank">Loyalty Program for WooCommerce</a></li>
            <li>Added compatibility with <a href="https://wordpress.org/plugins/woorewards/" target="_blank">WooRewards</a></li>
            <li>Added FAQ documentation which explains how to sort a Query Loop block by average rating, ranking, or number of reviews.</li>
            <li>Added GamiPress integration</li>
            <li>Added <a data-expand="#tools-import-product-reviews" href="<?php echo glsr_admin_url('tools'); ?>">Import Product Reviews</a> tool</li>
            <li>Added <a href="<?php echo glsr_admin_url('settings', 'integrations', 'woocommerce'); ?>">WooCommerce integration</a></li>
        </ul>

        <h4>🚫 Removed</h4>
        <ul>
            <li>Removed the GamiPress Reviews addon, it's now fully integrated into Site Reviews</li>
            <li>Removed the Woocommerce Reviews addon, it's now fully integrated into Site Reviews</li>
        </ul>

        <h4>🚨 Security</h4>
        <ul>
            <li>Fixed a <a href="https://patchstack.com/database/vulnerability/site-reviews/wordpress-site-reviews-plugin-6-2-0-unauth-csv-injection-vulnerability" target="_blank">CSV Injection vulnerability</a> which allowed malicious users to include formula values in reviews when exporting them to a CSV file.</li>
        </ul>

        <h4>🐞 Bugs Fixed</h4>
        <ul>
            <li>Fixed an issue causing a timeout when running plugin migrations</li>
            <li>Fixed compatibility with optimisation plugins which do not observe loading order of inline scripts</li>
            <li>Fixed compatibility with other reCAPTCHA plugins</li>
            <li>Fixed database table PRIMARY indexes on websites running MariaDB</li>
            <li>Fixed summary rating to use the decimal format of the website's locale</li>
            <li>Fixed the console logging, it now uses the saved log level</li>
            <li>Fixed translatable strings</li>
        </ul>
    </div>
</div>

