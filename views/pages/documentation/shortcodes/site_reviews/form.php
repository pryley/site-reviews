<p class="glsr-heading">form</p>
<?php if (glsr_addon_required('site-reviews-forms')) { ?>
    <?php echo wp_get_admin_notice(
        'The '.glsr_premium_link('site-reviews-forms').' addon is required to use this shortcode option.',
        ['type' => 'warning', 'additional_classes' => ['inline']]
    ); ?>
<?php } ?>
<p>Include the "form" option to display reviews using the review template of a custom Review Form. Accepted value is the Post ID of the custom Review Form.</p>
<p><span class="required">Important:</span> The review template of a custom Review Form will override the hide option used on this shortcode.</p>
<div class="shortcode-example">
    <pre><code class="language-shortcode">[site_reviews form="13"]</code></pre>
</div>
