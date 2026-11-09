<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox is-fullwidth open">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="true" aria-controls="upgrade-v9_0_0">
            <span class="title">Version 9.0</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="upgrade-v9_0_0" class="inside">

        <h2>Changes to how the scripts and stylesheets load</h2>
        <p><em>Likelihood Of Impact: <span class="impact-medium">Medium</span></em></p>
        <ol>
            <li>
                <p><strong>Site Reviews only loads its scripts and stylesheets on a page that shows reviews, a review form or a rating summary.</strong></p>
                <p>The public script is a small loader. The reviews, the form, the captcha and the modal are each a script module that the page loads when something on it needs that module. The stylesheet is split the same way and printed beside the first reviews, review form or rating summary on the page.</p>
            </li>
            <li>
                <p><strong>If your site uses a page cache, purge it after updating.</strong></p>
                <p>A cached page may link to a script or stylesheet that version 9.0 does not have. If this happens, the review elements on the page will look unstyled until the cache is refreshed.</p>
            </li>
            <li>
                <p><strong>If your theme uses <code>glsr_get_reviews()</code> or <code>glsr_get_review()</code> to display reviews, make sure that you wrap the output in a <code>&lt;div&gt;</code> with the <code>attributes</code> of the built object.</strong></p>
                <p>The attributes contain the script and stylesheet names needed to dynamically style the reviews.</p>
                <pre><code class="language-php">$reviewsHtml = $reviews->build();
echo "&lt;div {$reviewsHtml->attributes}&gt;";
echo $reviewsHtml;
echo $reviewsHtml->pagination;
echo '&lt;/div&gt;';</code></pre>
            </li>
        </ol>

        <h2>Changes to the admin</h2>
        <p><em>Likelihood Of Impact: <span class="impact-low">Low</span></em></p>
        <ol>
            <li>
                <p><strong>The Classic Editor no longer has a Site Reviews button.</strong></p>
                <p>To add reviews, a review form or a rating summary to a page in the Classic Editor, type the shortcode into the editor, for example <code>[site_reviews]</code>. The block editor's Site Reviews blocks have not changed.</p>
            </li>
            <li>
                <p><strong>The Sync Reviews tab of the Tools page has been removed.</strong></p>
                <p>It was built for an add-on that was never released.</p>
            </li>
            <li>
                <p><strong>The actions of the admin screens use the REST API of WordPress.</strong></p>
                <p>If a security plugin blocks the REST API for logged-in users, Site Reviews sends the same request through admin-ajax, so nothing changes for you. If you use a code snippet that sent one of these actions itself, it has to call the REST route instead; the routes are listed in the Site Reviews documentation.</p>
            </li>
        </ol>

        <h2>Removed hooks</h2>
        <p><em>Likelihood Of Impact: <span class="impact-low">Low</span></em></p>
        <ol>
            <li>
                <p><strong>The hook names that version 5.0 replaced no longer work.</strong></p>
                <p>Version 5.0 deprecated these in 2020, and they were kept working until now: <code>site-reviews/review/response</code>, <code>site-reviews/reviews/reviews-wrapper</code>, <code>site-reviews/review-table/filter</code>, <code>site-reviews/config/forms/submission-form</code>, <code>site-reviews/submission-form/order</code>, and the <code>{{ review_id }}</code> and <code>{{ assigned_to }}</code> template tags. The <code>site-reviews/support/deprecated/v5</code> filter hook is removed with them.</p>
            </li>
            <li>
                <p><strong>The hooks of the admin-ajax actions and of the Sync Reviews tab have been removed.</strong></p>
                <p>The <code>site-reviews/router/admin/unguarded-actions</code>, <code>site-reviews/addon/sync/enable</code> and <code>site-reviews/addon/sync/services</code> filter hooks, and the <code>site-reviews/register/tinymce</code>, <code>site-reviews/tinymce/editor-ids</code> and <code>site-reviews/tinymce/fields/{shortcode}</code> filter hooks of the Classic Editor button.</p>
            </li>
        </ol>

        <h2>Changes to Javascript events</h2>
        <p><em>Likelihood Of Impact: <span class="impact-low">Low</span></em></p>
        <ol>
            <li>
                <p><strong>The Javascript events have new names and each pass an object.</strong></p>
                <p>The previous names still work with the arguments they had except the <code>site-reviews/forms/init</code> and <code>block:site-reviews/…</code> events which no longer fire.</p>
                <pre><code class="language-js">/* version 8.3 */
GLSR.Event.on('site-reviews/form/handle', (response, form) => {
    // response.success, form is the &lt;form&gt; element
})

/* version 9.0 */
GLSR.Event.on('site-reviews/form/submitted', ({ form, response, success }) => {
    // form.el is the &lt;form&gt; element
})</code></pre>
                <table class="wp-list-table widefat striped">
                    <thead><tr><th>Version 8.3</th><th>Version 9.0</th></tr></thead>
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

/* version 9.0 */
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
                <p><strong>The <code>site-reviews/pagination/popstate</code> event has been removed.</strong></p>
                <p>Use the native browser event instead:</p>
                <pre><code class="language-js">window.addEventListener('popstate', (event) => {
    // the visitor used the back or the forward button
})</code></pre>
            </li>
        </ol>

        <h2>Changes to the Javascript API</h2>
        <p><em>Likelihood Of Impact: <span class="impact-low">Low</span></em></p>
        <ol>
            <li>
                <p><strong>The values that Site Reviews gives to its scripts have moved to <code>GLSR.config</code> and they are read-only.</strong></p>
                <p>The previous keys (for example <code>GLSR.ajax_url</code> and <code>GLSR.validation_strings</code>) no longer work. To change a value, use the <code>site-reviews/assets/config</code> filter hook:</p>
                <pre><code class="language-php">add_filter('site-reviews/assets/config', function (array $config, string $bundle) {
    if ('public' === $bundle) {
        $config['validation']['strings']['required'] = 'Please fill in this field.';
    }
    return $config;
}, 10, 2);</code></pre>
            </li>
            <li>
                <p><strong>The REST route of the shortcode options has a new address.</strong></p>
                <p>A code snippet that reads <code>site-reviews/v1/shortcode/{shortcode}?option={option}</code> reads <code>site-reviews/v1/shortcodes/{shortcode}/options/{option}</code> now. The previous address no longer answers.</p>
            </li>
            <li>
                <p><strong><code>GLSR.Request.send()</code> does more, and <code>GLSR.ajax</code> is gone from the admin.</strong></p>
                <p>A request can carry a JSON body, an abort signal and a route of <code>GLSR.config.request.routes</code> by its name, and a path that starts with <code>/</code> is read from the REST root, so a snippet can call a route of WordPress with the same fallback. A route in the <code>site-reviews/rest-api/routes</code> filter may be a method and a path, not only a path. A code snippet that used <code>GLSR.ajax</code> in the admin uses <code>GLSR.Request</code> with a REST route instead. The details are in the Site Reviews documentation.</p>
            </li>
            <li>
                <p><strong>Some keys of the <code>GLSR</code> object have been renamed and the previous keys no longer work.</strong></p>
                <p>For example, <code>GLSR.Utils</code> is now <code>GLSR.Util</code>, <code>GLSR.request</code> is now <code>GLSR.Request</code>, and <code>GLSR.forms</code> is now <code>GLSR.Form.instances</code>.</p>
                <p>To see if your site uses a previous key or a previous event name, turn on <strong>Debug Mode</strong> on the <?php echo glsr_admin_link('settings.advanced'); ?> page and look for warnings in the browser console. To see them for a single page view instead, add <code>?glsr-debug</code> to the address of the page. A code snippet that uses a previous hook name is noted on the <?php echo glsr_admin_link('tools.console'); ?> page.</p>
                <p>When no warning is left in either place, you can turn off <strong>Compatibility Mode</strong> on the <?php echo glsr_admin_link('settings.advanced'); ?> page. This also turns off the previous hook names.</p>
            </li>
        </ol>

    </div>
</div>
