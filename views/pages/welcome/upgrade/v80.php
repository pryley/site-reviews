<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox is-fullwidth open">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="true" aria-controls="upgrade-v8_0_0">
            <span class="title">Version 8.0</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="upgrade-v8_0_0" class="inside">

        <div class="glsr-notice-inline components-notice is-warning">
            <p class="components-notice__content">
                Site Reviews should automatically migrate itself after updating to v8.0. However, if you are experiencing problems after updating, you may need to manually run the <a href="<?php echo glsr_admin_url('tools', 'general'); ?>" data-expand="#tools-migrate-plugin">Migrate Plugin</a> tool.
            </p>
        </div>

        <h2>Changes to Block and Shortcode options</h2>
        <p><em>Likelihood Of Impact: <span class="impact-high">High</span></em></p>
        <ol>
            <li>
                <p><strong>The location of the CSS class attribute in a rendered block or shortcode has changed.</strong></p>
                <p>Previously, after adding a custom class to your block or shortcode, you would target the class attribute in your custom CSS like this:</p>
                <pre><code class="language-css">.glsr .glsr-summary.your-custom-class {}
.glsr .glsr-reviews.your-custom-class {}
.glsr .glsr-review-form.your-custom-class {}</code></pre>
                <p>Now, custom classes are added to the root element so you should do this instead:</p>
                <pre><code class="language-css">.your-custom-class .glsr-summary {}
.your-custom-class .glsr-reviews {}
.your-custom-class .glsr-review-form {}</code></pre>
                <p>If you are using any custom CSS to modify Site Reviews, you should verify that it still works as expected and update it if necessary.</p>
            </li>
        </ol>

        <h2>Changes to Plugin Templates</h2>
        <p><em>Likelihood Of Impact: <span class="impact-medium">Medium</span></em></p>
        <ol>
            <li>
                <p><strong>The HTML markup of the <code>form/field_checkbox.php</code> template has changed.</strong></p>
                <p>If you copied this template file to your theme, please update it.</p>
            </li>
            <li>
                <p><strong>The HTML markup of the <code>form/field_radio.php</code> template has changed.</strong></p>
                <p>If you copied this template file to your theme, please update it.</p>
            </li>
            <li>
                <p><strong>The HTML markup of the <code>form/submit-button.php</code> template has changed.</strong></p>
                <p>If you copied this template file to your theme, please update it.</p>
            </li>
            <li>
                <p><strong>The HTML markup of the <code>load-more-button.php</code> template has changed.</strong></p>
                <p>If you copied this template file to your theme, please update it.</p>
            </li>
            <li>
                <p><strong>The HTML markup of the <code>review.php</code> template has changed.</strong></p>
                <p>If you copied this template file to your theme, please update it.</p>
            </li>
        </ol>

        <h2>Action and Filter Hook changes</h2>
        <p><em>Likelihood Of Impact: <span class="impact-low">Low</span></em></p>
        <ol>
            <li>
                <p><strong>The <code>site-reviews/review-form/fields/normalized</code> filter hook has been removed.</strong></p>
                <p>If you were previously using this hook, you should change it to: <code>site-reviews/review-form/fields/all</code>.</p>
            </li>
        </ol>

    </div>
</div>
