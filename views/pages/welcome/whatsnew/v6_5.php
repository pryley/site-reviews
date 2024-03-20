<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox is-fullwidth">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="welcome-v6_5_0">
            <span class="title">Version 6.5</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="welcome-v6_5_0" class="inside">
        <p><em>Release Date &mdash; February 21st, 2023</em></p>
        <h4>✨ New Features</h4>
        <ul>
            <li>Added date and status options to the <a data-expand="#tools-export-reviews" href="<?php echo glsr_admin_url('tools'); ?>">Export Reviews</a> tool</li>
            <li>Added new <a data-expand="#shortcode-site_review" href="<?php echo glsr_admin_url('documentation', 'shortcodes'); ?>">[site_review]</a> shortcode to display a single review</li>
            <li>Added new "Single Review" block to display a single review</li>
            <li>Added new "Single Review" widget to display a single review</li>
            <li>Added new "Single Review" Elementor widget to display a single review</li>
        </ul>

        <h4>📢 Changed</h4>
        <ul>
            <li>Renamed the <code>site-reviews/rest-api/reviews/properties</code> hook to <code>site-reviews/rest-api/reviews/schema/properties</code></li>
            <li>Renamed the <code>site-reviews/rest-api/summary/properties</code> hook to <code>site-reviews/rest-api/summary/schema/properties</code></li>
        </ul>

        <h4>💅🏼 Improved</h4>
        <ul>
            <li>Improved sanitization of form values and helper function arguments</li>
            <li>Improved the System Info report</li>
        </ul>

        <h4>📦 Updated</h4>
        <ul>
            <li>Updated <a href="https://github.com/woocommerce/action-scheduler" target="_blank">Action Scheduler</a> to v3.5.4</li>
        </ul>

        <h4>🐞 Bugs Fixed</h4>
        <ul>
            <li>Fixed ability to press Enter when writing a response to a review using the inline editor on the All Reviews admin page</li>
            <li>Fixed button loading in the review form</li>
            <li>Fixed compatibility with Multilingual plugins (you should now be able to translate addons)</li>
            <li>Fixed compatibility with WordPress Multisite</li>
            <li>Fixed compatibility with Object Cache plugins (you should now be able to save the settings if you weren't able to before)</li>
            <li>Fixed HTML entities from breaking translations in the Strings settings</li>
            <li>Fixed PHP 8.1 deprecation notices</li>
            <li>Fixed PHP errors when changing the maximum rating with an unsupported filter hook</li>
            <li>Fixed support for rendering reviews with custom fields (Review Forms addon) and themes (Review Themes addon) using the helper functions</li>
            <li>Fixed the visibility of the Import Product Reviews tool</li>
        </ul>
    </div>
</div>

