<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox is-fullwidth open">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="true" aria-controls="welcome-v8_0_0">
            <span class="title">Version 8.0</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="welcome-v8_0_0" class="inside">
        <p><em>Release Date &mdash; March 3rd, 2026</em></p>
        <p>Site Reviews has a fresh coat of paint!</p>

        <h4>✨ New Features</h4>
        <ul>
            <li>Added <a href="https://avada.com/">Avada Builder</a> integration</li>
            <li>Added <a href="https://breakdance.com/">Breakdance</a> integration</li>
            <li>Added <a href="https://bricksbuilder.io/">Bricks</a> integration</li>
            <li>Added <a href="https://wordpress.org/plugins/cloudflare/">Cloudflare</a> integration</li>
            <li>Added <a href="https://www.elegantthemes.com/divi-5/">Divi 5</a> integration</li>
            <li>Added <a href="https://wordpress.org/plugins/duplicate-page/">Duplicate Page</a> integration</li>
            <li>Added <a href="https://themeforest.net/item/flatsome-multipurpose-responsive-woocommerce-theme/5484319">Flatsome Page Builder</a> integration</li>
            <li>Added <a href="https://multilingualpress.org/">MultilingualPress</a> integration</li>
            <li>Added <a href="https://profilepress.com/">ProfilePress</a> integration</li>
            <li>Added <a href="https://surecart.com/">SureCart</a> integration</li>
            <li>Added <a href="https://wpbakery.com/">WPBakery Page Builder</a> integration</li>
            <li>Added <a href="https://wployalty.net/">WPLoyalty</a> integration</li>
            <li>Added <a href="https://yoast.com/wordpress/plugins/seo-free/">Yoast SEO</a> integration</li>
            <li>Added a "CAPTCHA Placement" setting to change the position of the Captcha above or below the submit button in the review form</li>
            <li>Added a "verified" option to the Latest Reviews and Rating Summary blocks and shortcodes to display ratings and reviews based on the verified status.</li>
            <li>Added an "author" option to the Latest Reviews and Rating Summary blocks and shortcodes to display ratings and reviews submitted by a specific user.</li>
            <li>Added an "Autofill Fields" setting to automatically fill the name and email fields with the logged in user details.</li>
            <li>Added an "Enable Account Reviews" setting to the Ultimate Member integration to display a user's submitted reviews on their account page.</li>
            <li>Added an "Enable Session Storage" setting to persist entered review form values until either the review is submitted or the browser tab or window is closed</li>
            <li>Added basic style controls to the Gutenberg blocks</li>
            <li>Added Geolocation which allows you to display the location of the reviewer next to their name in the review (i.e. flag/country/state/city)</li>
            <li>Added more options to the Export Reviews tool</li>
            <li>Added the <code>summary_id</code> option to the Review Form, this allows you to update the rating summary immediately without reloading the page</li>
            <li>Added WP-CLI commands</li>
        </ul>

        <h4>💅🏼 Improved</h4>
        <ul>
            <li>Improved CSS of plugin styles for some themes</li>
            <li>Improved MIME type detection</li>
        </ul>

        <h4>⚠️ Changed</h4>
        <ul>
            <li>Changed the HTML markup of the <code>form/field_radio.php</code> template.</li>
            <li>Changed the HTML markup of the <code>form/submit-button.php</code> template.</li>
            <li>Changed the HTML markup of the <code>load-more-button.php</code> template.</li>
            <li>Changed the HTML markup of the <code>review.php</code> template.</li>
            <li>Changed the HTML markup of the <code>ultimatemember/reviews.php</code> template.</li>
            <li>Changed the location of the custom CSS class attribute in a rendered block or shortcode to the root DIV.</li>
        </ul>

        <h4>📦 Updated</h4>
        <ul>
            <li>Updated <a href="https://actionscheduler.org/">Action Scheduler</a> to v3.9.3</li>
            <li>Updated the Elementor integration</li>
            <li>Updated the Gutenberg blocks to API v3</li>
            <li>Updated the plugin documentation</li>
            <li>Updated the privacy policy example to include a section on geolocation</li>
            <li>Updated the WooCommerce integration</li>
        </ul>

        <h4>🚫 Removed</h4>
        <ul>
            <li>Removed the <code>site-reviews/review-form/fields/normalized</code> filter hook.</li>
            <li>Removed the <code>site-reviews/summary/counts</code> filter hook.</li>
        </ul>

        <h4>🐞 Bugs Fixed</h4>
        <ul>
            <li>Fixed Action Scheduler action redirects</li>
            <li>Fixed compatibility with the Salient theme</li>
            <li>Fixed Elementor template support in the schema parser</li>
            <li>Fixed generated CSS ID values to always be unique</li>
            <li>Fixed link focus when expanding excerpts with the keyboard</li>
            <li>Fixed the Blacklist validator</li>
            <li>Fixed the Cache integration to be more selective when flushing the cache</li>
            <li>Fixed the category priority feature</li>
            <li>Fixed the Cloudflare Turnstile integration</li>
            <li>Fixed the legacy WordPress widget options</li>
            <li>Fixed the pin control on the edit review page</li>
            <li>Fixed the RankMath integration when using Divi Builder</li>
            <li>Fixed the review form signature encryption</li>
        </ul>

    </div>
</div>
