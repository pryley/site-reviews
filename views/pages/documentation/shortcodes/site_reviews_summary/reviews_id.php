<p class="glsr-heading">reviews_id</p>
<div class="components-notice is-warning">
    <p class="components-notice__content">The <a href="<?= glsr_admin_url('addons'); ?>">Review Filters</a> add-on is required to use this shortcode option.</p>
</div>
<p>Include the "reviews_id" option to enable filtering the reviews by clicking the rating bars. Accepted values are <code>true</code> and <code>false</code>. Using this option will also enable AJAX filtering.</p>
<p>The default reviews_id value is: <code>""</code></p>
<div class="shortcode-example">
    <pre><code class="language-shortcode">[site_reviews_summary reviews_id="filtered-reviews"]</code></pre>
</div>
<div class="shortcode-example">
    <pre><code class="language-shortcode">[site_reviews id="filtered-reviews"]</code></pre>
</div>
