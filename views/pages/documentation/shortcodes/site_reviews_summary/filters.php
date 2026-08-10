<p class="glsr-heading">filters</p>
<?php if (glsr_addon_required('site-reviews-filters')) { ?>
    <?php echo wp_get_admin_notice(
        glsr_premium_link('site-reviews-filters').' addon is required to use this shortcode option.',
        ['type' => 'warning', 'additional_classes' => ['inline']]
    ); ?>
<?php } ?>
<p>Include the "filters" option to enable filtering the reviews by clicking the rating bars. Accepted values are <code>true</code> and <code>false</code>.</p>
<p>The default filters value is: <code>false</code></p>
<div class="shortcode-example">
    <pre><code class="language-shortcode">[site_reviews_summary filters="true"]</code></pre>
</div>
