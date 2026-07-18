<p class="glsr-heading">form</p>
<?php if (glsr_addon_required('site-reviews-forms')) { ?>
    <div class="glsr-notice-inline components-notice is-warning">
        <p class="components-notice__content">The <?php echo glsr_premium_link('site-reviews-forms'); ?> addon is required to use this shortcode option.</p>
    </div>
<?php } ?>
<p>Include the "form" option to display the review using the review template of a custom Review Form. Accepted value is the Post ID of the custom Review Form.</p>
<p><span class="required">Important:</span> The review template of a custom Review Form will override the hide option used on this shortcode.</p>
<div class="shortcode-example">
    <pre><code class="language-shortcode">[site_review form="13"]</code></pre>
</div>
