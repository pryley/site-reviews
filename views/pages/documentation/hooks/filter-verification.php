<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="hooks-manual-verification">
            <span class="title">Enable manual review verification</span>
            <span class="badge code">site-reviews/verification/enabled</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="hooks-manual-verification" class="inside">
        <div class="glsr-notice-inline components-notice is-warning">
            <p class="components-notice__content">The recommended method for verifying reviews is to enable the <code><a href="<?php echo glsr_admin_url('settings', 'general'); ?>">Request Verification</a></code> setting which automatically sends an email to reviewers asking them to verify their review.</p>
        </div>
        <p>Use this hook to enable the ability to manually verify reviews from the WordPress Admin.</p>
        <pre><code class="language-php">/**
 * Enables manual verification of reviews.
 * Paste this in your active theme's functions.php file.
 * @return bool
 */
add_filter('site-reviews/verification/enabled', '__return_true');</code></pre>
    </div>
</div>
