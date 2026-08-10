<p class="glsr-heading">theme</p>
<?php if (glsr_addon_required('site-reviews-themes')) { ?>
    <?php echo wp_get_admin_notice(
        'The '.glsr_premium_link('site-reviews-themes').' addon is required to use this shortcode option.',
        ['type' => 'warning', 'additional_classes' => ['inline']]
    ); ?>
<?php } ?>
<p>Include the "theme" option to display the review using a custom Review Theme. Accepted value is the Post ID of the custom Review Theme.</p>
<p><span class="required">Important:</span> The custom Review Theme will override the hide option used on this shortcode.</p>
<div class="shortcode-example">
    <pre><code class="language-shortcode">[site_review theme="13"]</code></pre>
</div>
