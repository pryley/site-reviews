<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="faq-enable-manual-verification">
            <span class="title">How do I enable manual review verification?</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="faq-enable-manual-verification" class="inside">
        <p>To verify your reviews, enable the "Request Verification" setting, this will send a verification email to the person who submitted the review. Once they have clicked the "Verify Review" link in the email, their review will be marked as verified.</p>
        <p>If you would prefer to manually verify the reviews on your website, then you can use this code snippet to enable manual review verification:</p>
        <pre><code class="language-php">/**
 * Enables manual review verification from the /wp-admin/
 */
add_filter('site-reviews/enable/verification', '__return_true');</code></pre>
    </div>
</div>
