<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox is-fullwidth">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="welcome-v6_0_0">
            <span class="title">Version 6.0</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="welcome-v6_0_0" class="inside">
        <p><em>Release Date &mdash; September 14th, 2022</em></p>

        <h4>✨ New Features</h4>
        <ul>
            <li>Added a deactivation feedback dialog</li>
            <li>Added a "Limit Reviews For" setting which allows you to set a time-limit (in days) for Review Limits</li>
            <li>Added ability to mark reviews as "Verified"</li>
            <li>Added ability to search reviews by ID on the All Reviews admin page</li>
            <li>Added automatic conversion of UTF-16/UTF-32 encoded CSV files when importing reviews</li>
            <li>Added custom capabilities for review categories</li>
            <li>Added experimental filter hooks to combine css and javascript files when using addons (see <a data-expand="#hooks-filter-combine-assets" href="<?php echo glsr_admin_url('documentation', 'hooks'); ?>">Hooks documentation</a>)</li>
            <li>Added migration and nonce support for <a href="https://wordpress.org/plugins/litespeed-cache/" rel="nofollow" target="_blank">LiteSpeed Cache</a> (flushes the cache after migration)</li>
            <li>Added migration support for <a href="https://wp-rocket.me/" rel="nofollow" target="_blank">WP Rocket</a> (flushes the cache after migration)</li>
            <li>Added support for the Bluehost <a href="https://github.com/bluehost/endurance-page-cache" rel="nofollow" target="_blank">Endurance Page Cache</a> must-use plugin</li>
            <li>Added the Elementor Pro plugin style</li>
            <li>Added the <code>rating_field</code> option to the Summary block and Elementor widget</li>
            <li>Added the <code>reviews_id</code> option to the Review Form, this allows you to display submitted reviews immediately without reloading the page.</li>
        </ul>

        <h4>📢 Changed</h4>
        <ul>
            <li>🚨 Requires at least PHP v7.2</li>
            <li>🚨 Requires at least WordPress v5.8</li>
            <li>🚨 Changed the Divi plugin style to use the Divi Gallery pagination style for paginating reviews</li>
            <li>🚨 Changed the review title tag from &lt;h3&gt; to &lt;h4&gt;. If you need to change it back, please see the <a data-expand="#faq-change-review-title-tag" href="<?php echo glsr_admin_url('documentation', 'faq'); ?>">FAQ</a> help page.</h4>
            <li>🚨 Changed the strings "← Previous" and "Next →" to "Previous" and "Next". If you have customised these strings in the settings, please <a href="<?php echo glsr_admin_url('settings', 'strings'); ?>">update them</a>.</li>
            <li>🚨 Rewrote the button and pagination loading animations. If you are using a custom Site Reviews pagination template in your child theme, please remove the <code>{{ loader }}</code> template tag.</li>
            <li>Enabled SSL verification on all requests (this can be disabled with the WordPress <a href="http://developer.wordpress.org/reference/hooks/https_ssl_verify/" rel="nofollow" target="_blank">https_ssl_verify</a> filter hook)</li>
            <li>Optimised the javascript file sizes</li>
            <li>Renamed the "Submissions" settings page to "Forms"</li>
            <li>Rewrote the frontend style to use CSS variables, this should make CSS tweaks easier to write</li>
            <li>Rewrote the review modals to support the new review image galleries (<a href="https://niftyplugins.com/plugins/site-reviews-images/" target="_blank">Review Images</a> addon required)</li>
            <li>Submit and load more buttons now make use of the WordPress Block button classes by default</li>
            <li>Updated the Bootstrap plugin styles to Bootstrap v5</li>
        </ul>

        <h4>🚫 Removed</h4>
        <ul>
            <li>🚨 Removed support for Internet Explorer</li>
            <li>🚨 Removed support for PHP 5.6, 7.0, and 7.1</li>
            <li>🚨 Removed the Polyfill.io script (since it was used for IE support)</li>
            <li>🚨 Removed the <code>site-reviews/after/submission</code> javascript event (if you were using this event, please see the <a data-expand="#upgrade-v6_0_0" href="<?php echo glsr_admin_url('welcome', 'upgrade-guide'); ?>">Upgrade guide</a>)</li>
        </ul>

        <h4>🐞 Bugs Fixed</h4>
        <ul>
            <li>Fixed compatibility with <a href="https://wordpress.org/plugins/duplicate-post/" target="_blank">Yoast Duplicate Post</a></li>
            <li>Fixed invalid "deprecated" entries which were being added to the Console on some websites</li>
            <li>Fixed last name initials when there is only a first name</li>
            <li>Fixed permissions for plugin pages</li>
            <li>Fixed plugin redirection when bulk activating multiple plugins</li>
            <li>Fixed primary keys on custom database pivot tables</li>
            <li>Fixed review importing to skip empty CSV rows without throwing an error</li>
            <li>Fixed review migration of Site Reviews v4 reviews</li>
            <li>Fixed the blocks in the Customizer widget panel</li>
            <li>Fixed the Elementor integration which broke some other Elementor widgets</li>
            <li>Fixed the <a data-expand="#fn-glsr_create_review" href="<?php echo glsr_admin_url('documentation', 'functions'); ?>"><code>glsr_create_review</code></a> function to bypass the "Require Approval" setting</li>
            <li>Fixed the star rating field for some themes</li>
            <li>Fixed the Version value in the System Info</li>
            <li>Fixed the WPML integration</li>
        </ul>
    </div>
</div>

