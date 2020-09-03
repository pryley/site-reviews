<div id="hooks-06" class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="">
            <span class="title">Do something immediately after a review has been submitted</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div class="inside">
        <p>Use this hook if you want to do something immediately after a review has been successfully submitted.</p>
        <p>The <code>$review</code> object is the review that was created. The <code>$request</code> object is the request that was submitted to create the review.</p>
        <pre><code class="language-php">/**
 * Runs after a review has been submitted in Site Reviews.
 * Paste this in your active theme's functions.php file.
 * @param \GeminiLabs\SiteReviews\Review $review
 * @param \GeminiLabs\SiteReviews\Arguments $reviewValues
 * @param \GeminiLabs\SiteReviews\Commands\CreateReview $command
 * @return void
 */
add_action('site-reviews/review/created', function ($review, $reviewValues, $command) {
    // do something here.
}, 10, 3);</code></pre>
    </div>
</div>
