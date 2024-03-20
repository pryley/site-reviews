<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="faq-redirect-after-submission">
            <span class="title">How do I redirect to a custom URL after a form is submitted?</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="faq-redirect-after-submission" class="inside">
        <ol>
            <li>
                <p>To redirect the page after a form has been submitted, edit the page the shortcode is on and use the <a href="https://codex.wordpress.org/Using_Custom_Fields#Usage" target="_blank">Custom Fields</a> metabox to add a <code>redirect_to</code> as the Custom Field name and the URL you want to redirect to as the value.</p>
            </li>
            <li>
                <p>If you need to redirect to a location based on the rating that was given, you can also use the following hook:</p>
                <pre><code class="language-php">/**
 * Add a conditional redirect based on the rating
 * 1. Uses the documented "redirect_to" meta_key for all reviews with a 4-5 star rating
 * 2. Uses a "redirect_if_bad_rating" meta_key for all reviews with a 1-3 star rating
 * @param string $redirectUrl This is the URL that you saved to the page with the "redirect_to" meta_key
 * @param \GeminiLabs\SiteReviews\Commands\CreateReview $command
 * @param \GeminiLabs\SiteReviews\Review $review
 * @return string
 */
add_filter('site-reviews/review/redirect', function ($redirectUrl, $command, $review) {
    if ($review->rating < 4) {
        if ($url = get_post_meta($command->post_id, 'redirect_if_bad_rating', true)) {
            return add_query_arg('review_id', $review->ID, $url);
        }
    }
    return $redirectUrl;
}, 10, 3);</code></pre>
                <p>Since we are passing the Review ID in the "redirect_if_bad_rating" redirect URL, you can do something like this to get the review details on that page:</p>
                <pre><code class="language-php">$review = apply_filters('glsr_get_review', null, filter_input(INPUT_GET, 'review_id', FILTER_VALIDATE_INT));
if ($review) {
    glsr_debug($review);
}</code></pre>
                <p>To learn more about the <code><a href="<?php echo glsr_admin_url('documentation', 'functions'); ?>" data-expand="#fn-glsr_get_review">glsr_get_review()</a></code> function, please refer to the Functions documentation page.</p>
            </li>
        </ol>
    </div>
</div>
