<p class="glsr-heading">reviews_id</p>
<?php if (glsr_addon_required('site-reviews-filters')) { ?>
    <?php echo wp_get_admin_notice(
        glsr_premium_link('site-reviews-filters').' addon is required to use this shortcode option.',
        ['type' => 'warning', 'additional_classes' => ['inline']]
    ); ?>
<?php } ?>
<p>Include the "reviews_id" option to enable filtering the reviews by clicking the rating bars. Accepted values are <code>true</code> and <code>false</code>. Using this option will also enable AJAX filtering.</p>
<div class="shortcode-example">
    <pre><code class="language-shortcode">[site_reviews_summary reviews_id="rating-summary"]</code></pre>
</div>
<div class="shortcode-example">
    <pre><code class="language-shortcode">[site_reviews id="rating-summary"]</code></pre>
</div>
