<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox is-fullwidth">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="welcome-v7_0_0">
            <span class="title">Version 7.0</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="welcome-v7_0_0" class="inside">
        <p><em>Release Date &mdash; May 3rd, 2024</em></p>

        <h4>✨ New Features</h4>
        <ul>
            <li>Added an example CSV file that can be downloaded on the Import Reviews tool</li>
            <li>Added <a href="https://avada.com/" target="_blank">Avada Fusion Builder</a> elements</li>
            <li>Added basic style controls to the Elementor widgets</li>
            <li>Added <a data-expand="#tools-ip-detection" href="<?php echo glsr_admin_url('tools', 'general'); ?>">Configure IP Address Detection</a> tool</li>
            <li>Added Dashboard widget</li>
            <li>Added exponential-backoff strategy to API calls</li>
            <li>Added <a href="https://crocoblock.com/plugins/jetwoobuilder/" target="_blank">JetWooBuilder</a> integration</li>
            <li>Added Migrate Product Ratings to the Import Roduct Reviews tool (allows third-party plugins to filter products by rating)</li>
            <li>Added Reviews metabox to WooCommerce product pages</li>
            <li>Added <a href="https://wordpress.org/plugins/wp-seopress/" target="_blank">SEOPress</a> integration</li>
            <li>Added support for conditional fields in review forms (<a href="https://niftyplugins.com/plugins/site-reviews-forms/" target="_blank">Review Forms</a> addon required)</li>
            <li>Added support for multilingual categories</li>
            <li>Added support for SQLite databases</li>
            <li>Added the <a href="https://niftyplugins.com/plugins/site-reviews-actions/" target="_blank">Review Actions</a> addon</li>
        </ul>

        <h4>📢 Changed</h4>
        <ul>
            <li>⚠️ Changed the minimum required version of PHP to v7.4</li>
            <li>⚠️ Changed the minimum required version of WordPress to v6.1</li>
            <li>⚠️ Moved all CSS variables from <code>:root {}</code> to <code>body {}</code></li>
            <li>Updated Action Scheduler to v3.7.4</li>
        </ul>

        <h4>🚫 Removed</h4>
        <ul>
            <li>Removed the email and IP address values from the review in javascript responses</li>
            <li>Removed the Site Reviews widgets from the Legacy Widget block</li>
        </ul>

        <h4>🐞 Bugs Fixed</h4>
        <ul>
            <li>Fixed a Cross Site Scripting (XSS) vulnerability caused by users saving malicious javascript text to their first/last/display name in their WordPress user profile.</li>
            <li>Fixed a Cross Site Scripting (XSS) vulnerability which allowed authenticated admin users to insert javascript into review content.</li>
            <li>Fixed a race-time vulnerability from single-packet attacks (this should improve spam protection)</li>
            <li>Fixed bulk-delete of scheduled actions</li>
            <li>Fixed cache plugin integrations</li>
            <li>Fixed compatibility with the <a href="https://marketpress.com/shop/plugins/cookie-cracker/" target="_blank">Cookie Cracker</a> plugin</li>
            <li>Fixed Discord notifications for reviews with more than 2000 characters</li>
            <li>Fixed display of rating stars in review revisions</li>
            <li>Fixed Divi button style</li>
            <li>Fixed license key sanitization</li>
            <li>Fixed localized rating values</li>
            <li>Fixed notifications from triggering when a review is auto-saved as a draft</li>
            <li>Fixed notifications from triggering when reviews are imported</li>
            <li>Fixed pinned reviews when the WooCommerce integration is enabled</li>
            <li>Fixed quick/bulk editing of WooCommerce Products from automatically disabling reviews support</li>
            <li>Fixed review dates from converting the date to the site's timezone</li>
            <li>Fixed review excerpts in cases when the PHP Intl extension is misconfigured</li>
            <li>Fixed review responses disappearing when editing reviews on the frontend (<a href="https://niftyplugins.com/plugins/site-reviews-authors/" target="_blank">Review Authors</a> addon required)</li>
            <li>Fixed SQL error when filtering reviews by "No author"</li>
            <li>Fixed the {review_link} notification tag</li>
            <li>Fixed the "new item" labels for the Site Reviews post_type and taxonomy</li>
            <li>Fixed the validation message setting with WPML/Polylang</li>
            <li>Fixed unicode support for names in reviews</li>
            <li>Fixed <a href="https://wordpress.org/plugins/woorewards/" target="_blank">WooRewards</a> integration</li>
        </ul>
    </div>
</div>
