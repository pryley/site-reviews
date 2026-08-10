<p class="glsr-heading">theme</p>
<?php if (glsr_addon_required('site-reviews-themes')) { ?>
    <?php echo wp_get_admin_notice(
        glsr_premium_link('site-reviews-themes').' addon is required to use this shortcode option.',
        ['type' => 'warning', 'additional_classes' => ['inline']]
    ); ?>
<?php } ?>
<p>Include the "theme" option to use the rating style of a custom Review Theme. Accepted value is the Post ID or slug of the custom Review Theme.</p>
<div class="shortcode-example">
    <pre><code class="language-shortcode">[site_reviews_form theme="13"]</code></pre>
</div>
