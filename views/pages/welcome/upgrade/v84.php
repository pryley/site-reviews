<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox is-fullwidth open">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="true" aria-controls="upgrade-v8_4_0">
            <span class="title">Version 8.4</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="upgrade-v8_4_0" class="inside">

        <h2>Changes to Javascript events</h2>
        <p><em>Likelihood Of Impact: <span class="impact-low">Low</span></em></p>
        <ol>
            <li>
                <p><strong>The Javascript events have new names, and each passes one object.</strong></p>
                <p>The previous names still work, with the arguments they had. Use the new names in anything you write:</p>
                <pre><code class="language-js">/* version 8.3 */
GLSR.Event.on('site-reviews/form/handle', (response, form) => {
    // response.success, form is the &lt;form&gt; element
})

/* version 8.4 */
GLSR.Event.on('site-reviews/form/submitted', ({ form, response, success }) => {
    // form.el is the &lt;form&gt; element
})</code></pre>
                <table class="wp-list-table widefat striped">
                    <thead><tr><th>Version 8.3</th><th>Version 8.4</th></tr></thead>
                    <tbody>
                        <tr><td><code>site-reviews/loaded</code></td><td><code>site-reviews/initialized</code></td></tr>
                        <tr><td><code>site-reviews/excerpts/init</code>, <code>site-reviews/modal/init</code>, <code>site-reviews/pagination/init</code></td><td><code>site-reviews/review/initialized</code></td></tr>
                        <tr><td><code>site-reviews/pagination/handle</code></td><td><code>site-reviews/review/paginated</code></td></tr>
                        <tr><td><code>site-reviews/forms/init</code></td><td><code>site-reviews/form/initialized</code></td></tr>
                        <tr><td><code>site-reviews/form/handle</code></td><td><code>site-reviews/form/submitted</code></td></tr>
                        <tr><td><code>site-reviews/modal/open</code></td><td><code>site-reviews/modal/opened</code></td></tr>
                        <tr><td><code>site-reviews/modal/close</code></td><td><code>site-reviews/modal/closed</code></td></tr>
                    </tbody>
                </table>
                <p>The <code>site-reviews/init</code> event has not changed.</p>
            </li>
            <li>
                <p><strong>The <code>site-reviews/init</code> and <code>site-reviews/loaded</code> events are no longer triggered after a review is submitted.</strong></p>
                <p>Previously, a submitted review form set up everything on the page again, which triggered both events. Now, the form only sets up the reviews and the summary that it updates.</p>
                <p>If you use one of these events in a code snippet to run something after each submission, use the <code>site-reviews/review/initialized</code> event for the updated reviews, and the <code>site-reviews/summary/updated</code> event for the updated summary:</p>
                <pre><code class="language-js">/* version 8.3 */
GLSR.Event.on('site-reviews/init', () => {
    // runs when the page loads, and after each submission
})

/* version 8.4 */
GLSR.Event.on('site-reviews/review/initialized', ({ root }) => {
    // runs when the page loads (root is the document), and with the list of
    // reviews that was updated (root) after a submission or a page change
})
GLSR.Event.on('site-reviews/summary/updated', ({ summary }) => {
    // runs after a submission updates a summary; summary.el is its element
})</code></pre>
                <p>Both events are still triggered when the page loads.</p>
            </li>
            <li>
                <p><strong>The <code>site-reviews/pagination/popstate</code> event is removed.</strong></p>
                <p>It repeated the browser's own event. Use that instead:</p>
                <pre><code class="language-js">window.addEventListener('popstate', (event) => {
    // the visitor used the back or the forward button
})</code></pre>
            </li>
        </ol>

        <h2>Changes to the Javascript API</h2>
        <p><em>Likelihood Of Impact: <span class="impact-low">Low</span></em></p>
        <ol>
            <li>
                <p><strong>The values that Site Reviews gives to its scripts have moved to <code>GLSR.config</code>, and they are read-only.</strong></p>
                <p>The previous keys (for example <code>GLSR.ajax_url</code> and <code>GLSR.validation_strings</code>) still work, but changing one of them in the browser no longer has an effect. To change a value, use the <code>site-reviews/assets/config</code> filter hook:</p>
                <pre><code class="language-php">add_filter('site-reviews/assets/config', function (array $config, string $bundle) {
    if ('public' === $bundle) {
        $config['validation']['strings']['required'] = 'Please fill in this field.';
    }
    return $config;
}, 10, 2);</code></pre>
            </li>
            <li>
                <p><strong>Some keys of the <code>GLSR</code> object have been renamed.</strong></p>
                <p>For example, <code>GLSR.Utils</code> is now <code>GLSR.Util</code>, <code>GLSR.request</code> is now <code>GLSR.Request</code>, and <code>GLSR.forms</code> is now <code>GLSR.Form.instances</code>. The previous keys still work.</p>
                <p>To see if your site uses a previous key or a previous event name, turn on <strong>Debug Mode</strong> on the <?php echo glsr_admin_link('settings.advanced'); ?> page and look for warnings in the browser console. To see them for a single page view instead, add <code>?glsr-debug</code> to the address of the page.</p>
                <p>When no warning is left, you can turn off <strong>Compatibility Mode</strong> on the same page.</p>
            </li>
        </ol>

    </div>
</div>
