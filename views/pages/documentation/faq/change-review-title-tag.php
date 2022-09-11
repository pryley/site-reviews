<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="faq-change-review-title-tag">
            <span class="title">How do I change the &lt;h4&gt; tag used in the review title?</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="faq-change-review-title-tag" class="inside">
        <pre><code class="language-php">/**
 * Changes the &lt;h4&gt; tag used for review titles to &lt;h3&gt;.
 * Paste this in your active theme's functions.php file.
 * @param string $field
 * @return string
 */
add_filter('site-reviews/review/build/tag/title', function ($field) {
    return str_replace(['&lt;h4','h4&gt;'], ['&lt;h3','h3&gt;'], $field);
});</code></pre>
    </div>
</div>
