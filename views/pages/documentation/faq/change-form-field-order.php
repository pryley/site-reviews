<div id="faq-change-form-field-order" class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="">
            <span class="title">How do I change the order of the submission form fields?</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div class="inside">
        <p>To customise the order of the fields in the review submission form, use the <code><a href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=documentation#tab-hooks'); ?>" data-expand="#hooks-01">site-reviews/submission-form/order</a></code> filter hook in your theme's <code>functions.php</code> file.</p>
        <pre><code class="language-php">/**
 * Customises the order of the fields used in the Site Reviews submission form.
 * Paste this in your active theme's functions.php file.
 * @param array $order
 * @return array
 */
add_filter('site-reviews/submission-form/order', function ($order) {
    // The $order array contains the field keys returned below.
    // Simply change the order of the field keys to the desired field order.
    return [
        'rating',
        'title',
        'content',
        'name',
        'email',
        'terms',
    ];
});</code></pre>
        <p>If you have used the example above and the submission-form fields are not working correctly, check the <code><a href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=tools#tab-console'); ?>">Tools &rarr; Console</a></code> for errors.</p>
    </div>
</div>
