<?php defined('ABSPATH') || exit; ?>

<p class="glsr-heading">reviews_id</p>
<p>Include the "reviews_id" option to load the submitted review into an existing [site_reviews] shortcode on the same page which uses that id option value.</p>
<p>Note: using this option may not be a good idea if your review form is located below the reviews on your page as it will cause the page layout to shift, and the form's success message may be moved out of view.</p>
<div class="shortcode-example">
    <pre><code class="language-shortcode">[site_reviews_form reviews_id="reviews"]</code></pre>
</div>
<div class="shortcode-example">
    <pre><code class="language-shortcode">[site_reviews id="reviews"]</code></pre>
</div>
