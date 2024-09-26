<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="hooks-modify-review-request">
            <span class="title">Modify the submitted review request</span>
            <span class="badge code">site-reviews/review/request</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="hooks-modify-review-request" class="inside">
        <p>Use this hook if you want to modify the request of a submitted review. This hook is similar to the <a data-expand="#hooks-filter-submitted-review-values" href="<?php echo glsr_admin_url('documentation', 'hooks'); ?>">site-reviews/create/review-values</a> hook, except that it allows you to directly modify the raw Request object before the values are sanitized.</p>
        <pre><code class="language-php">/**
 * Paste this in your active theme's functions.php file.
 * @param \GeminiLabs\SiteReviews\Request $request
 * @return void
 */
add_action('site-reviews/review/request', function ($request) {
    // modify the $request object here, you do this by using the "get" and "set" methods of the object.
    // For example:
    $assignedPosts = $request->get('assigned_posts'); // this is a comma-separated string, not an array!
    if ('13' === $assignedPosts) {
        $request->set('assigned_posts', '13,14,15');
    }
});</code></pre>
    </div>
</div>
