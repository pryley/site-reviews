<?php defined('WPINC') || die; ?>

<p>Hooks (also known as <a href="https://developer.wordpress.org/plugins/hooks/index.html">Actions and Filters</a>) are used in your theme's <code>functions.php</code> file to make changes to the plugin.</p>

<div id="hooks-01" class="glsr-card postbox">
    <div class="glsr-card-header">
        <h3>Customise the order of the fields in the review submission form</h3>
        <button type="button" class="handlediv" aria-expanded="true">
            <span class="screen-reader-text"><?= __('Toggle documentation panel', 'site-reviews'); ?></span>
            <span class="toggle-indicator" aria-hidden="true"></span>
        </button>
    </div>
    <div class="inside">
        <p>Use this hook to customise the order of the fields in the review submission form used by Site Reviews.</p>
        <p>See the <code><a data-expand="#faq-07" href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=documentation#tab-faq'); ?>">FAQ</a></code> for a detailed example of how to use this hook.</p>
        <pre><code class="php">/**
 * Customises the order of the fields used in the Site Reviews submission form.
 * Paste this in your active theme's functions.php file.
 * @param array $order
 * @return array
 */
add_filter('site-reviews/submission-form/order', function ($order) {
    // modify the $order array here
    return $order;
});</code></pre>
    </div>
</div>

<div id="hooks-02" class="glsr-card postbox">
    <div class="glsr-card-header">
        <h3>Customise the star images</h3>
        <button type="button" class="handlediv" aria-expanded="true">
            <span class="screen-reader-text"><?= __('Toggle documentation panel', 'site-reviews'); ?></span>
            <span class="toggle-indicator" aria-hidden="true"></span>
        </button>
    </div>
    <div class="inside">
        <p>Use this hook to customise the star images used by Site Reviews.</p>
        <p>See the <code><a data-expand="#faq-11" href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=documentation#tab-faq'); ?>">FAQ</a></code> for a detailed example of how to use this hook.</p>
        <pre><code class="php">/**
 * Customises the stars used by Site Reviews.
 * Paste this in your active theme's functions.php file.
 * @param array $config
 * @return array
 */
add_filter('site-reviews/config/inline-styles', function ($config) {
    // modify the star URLs in the $config array here
    return $config;
});</code></pre>
    </div>
</div>

<div id="hooks-03" class="glsr-card postbox">
    <div class="glsr-card-header">
        <h3>Disable the plugin javascript</h3>
        <button type="button" class="handlediv" aria-expanded="true">
            <span class="screen-reader-text"><?= __('Toggle documentation panel', 'site-reviews'); ?></span>
            <span class="toggle-indicator" aria-hidden="true"></span>
        </button>
    </div>
    <div class="inside">
        <p>Use this hook if you want to disable the plugin javascript from loading on your website.</p>
        <pre><code class="php">/**
 * Disables the Site Reviews javascript.
 * Paste this in your active theme's functions.php file.
 * @return bool
 */
add_filter('site-reviews/assets/js', '__return_false');</code></pre>
    </div>
</div>

<div id="hooks-04" class="glsr-card postbox">
    <div class="glsr-card-header">
        <h3>Disable the plugin stylesheet</h3>
        <button type="button" class="handlediv" aria-expanded="true">
            <span class="screen-reader-text"><?= __('Toggle documentation panel', 'site-reviews'); ?></span>
            <span class="toggle-indicator" aria-hidden="true"></span>
        </button>
    </div>
    <div class="inside">
        <p>Use this hook if you want to disable the plugin stylesheet from loading on your website.</p>
        <pre><code class="php">/**
 * Disables the Site Reviews stylesheet.
 * Paste this in your active theme's functions.php file.
 * @return bool
 */
add_filter('site-reviews/assets/css', '__return_false');</code></pre>
    </div>
</div>

<div id="hooks-05" class="glsr-card postbox">
    <div class="glsr-card-header">
        <h3>Disable the polyfill.io script</h3>
        <button type="button" class="handlediv" aria-expanded="true">
            <span class="screen-reader-text"><?= __('Toggle documentation panel', 'site-reviews'); ?></span>
            <span class="toggle-indicator" aria-hidden="true"></span>
        </button>
    </div>
    <div class="inside">
        <p>Use this hook if you want to disable the polyfill.io script from loading on your website.</p>
        <p><span class="required">Important:</span> The polyfill.io script provides support for Internet Explorer versions 9-10. If you disable it, Site Reviews will no longer work in those browsers.</p>
        <pre><code class="php">/**
 * Disables the polyfill.io script in Site Reviews.
 * Paste this in your active theme's functions.php file.
 * @return bool
 */
add_filter('site-reviews/assets/polyfill', '__return_false');</code></pre>
    </div>
</div>

<div id="hooks-06" class="glsr-card postbox">
    <div class="glsr-card-header">
        <h3>Do something immediately after a review has been submitted</h3>
        <button type="button" class="handlediv" aria-expanded="true">
            <span class="screen-reader-text"><?= __('Toggle documentation panel', 'site-reviews'); ?></span>
            <span class="toggle-indicator" aria-hidden="true"></span>
        </button>
    </div>
    <div class="inside">
        <p>Use this hook if you want to do something immediately after a review has been successfully submitted.</p>
        <p>The <code>$review</code> object is the review that was created. The <code>$request</code> object is the request that was submitted to create the review.</p>
        <pre><code>/**
 * Runs after a review has been submitted in Site Reviews.
 * Paste this in your active theme's functions.php file.
 * @param \GeminiLabs\SiteReviews\Review $review
 * @param \GeminiLabs\SiteReviews\Commands\CreateReview $request
 * @return void
 */
add_action('site-reviews/review/created', function ($review, $request) {
    // do something here.
}, 10, 2);</code></pre>
    </div>
</div>

<div id="hooks-07" class="glsr-card postbox">
    <div class="glsr-card-header">
        <h3>Modify the schema</h3>
        <button type="button" class="handlediv" aria-expanded="true">
            <span class="screen-reader-text"><?= __('Toggle documentation panel', 'site-reviews'); ?></span>
            <span class="toggle-indicator" aria-hidden="true"></span>
        </button>
    </div>
    <div class="inside">
        <p>Use this hook if you would like to modify the schema type properties.</p>
        <p>This hook is specific to the schema type. For example, to modify the schema properties for the LocalBusiness schema type you would use the <code>site-reviews/schema/LocalBusiness</code> hook, but to modify the schema properties for the Product schema type you would use the <code>site-reviews/schema/Product</code> hook.</p>
        <p>See the <code><a data-expand="#faq-01" href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=documentation#tab-faq'); ?>">FAQ</a></code> for a detailed example of how to use this hook.</p>
        <p>Make sure to use Google's <a href="https://search.google.com/structured-data/testing-tool">Structured Data Testing Tool</a> to test the schema after any custom modifications have been made.</p>
        <pre><code class="php">/**
 * Modifies the properties of the schema created by Site Reviews.
 * Change "LocalBusiness" to the schema type you wish to change (i.e. Product)
 * Paste this in your active theme's functions.php file.
 * @param array $schema
 * @return array
 */
add_filter('site-reviews/schema/LocalBusiness', function ($schema) {
    // modify the $schema array here.
    return $schema;
});</code></pre>
    </div>
</div>

<div id="hooks-08" class="glsr-card postbox">
    <div class="glsr-card-header">
        <h3>Modify the submitted review before it is saved</h3>
        <button type="button" class="handlediv" aria-expanded="true">
            <span class="screen-reader-text"><?= __('Toggle documentation panel', 'site-reviews'); ?></span>
            <span class="toggle-indicator" aria-hidden="true"></span>
        </button>
    </div>
    <div class="inside">
        <p>Use this hook if you want to modify the values of the submitted review before the review is created.</p>
        <pre><code>/**
 * Modifies the review values before they are saved
 * Paste this in your active theme's functions.php file.
 * @param array $reviewValues
 * @return array
 */
add_filter('site-reviews/create/review-values', function ($reviewValues) {
    // modify the $reviewValues array here
    return $reviewValues;
});</code></pre>
    </div>
</div>
