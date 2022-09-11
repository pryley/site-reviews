<?php defined('ABSPATH') || exit; ?>

<p class="glsr-heading">reviews_id</p>
<p>Include the "reviews_id" option to load the submitted review into an existing [site_reviews] shortcode on the same page which uses that id option value.</p>
<p>Note: using this option may not be a good idea if your review form is located below the reviews on your page as it will cause the page layout to shift, and the form's success message may be moved out of view.</p>
<p>The default reviews_id value is: <code>""</code></p>
<div class="shortcode-example">
    <input type="text" readonly class="code" value='[site_reviews_form reviews_id="our-reviews"]'>
    <pre><code class="syntax-shortcode"><span class="tag">[site_reviews_form</span> <span class="attr-name">reviews_id</span>=<span class="attr-value">"our-reviews"</span><span class="tag">]</span></code></pre>
</div>
<div class="shortcode-example">
    <input type="text" readonly class="code" value='[site_reviews id="our-reviews"]'>
    <pre><code class="syntax-shortcode"><span class="tag">[site_reviews</span> <span class="attr-name">id</span>=<span class="attr-value">"our-reviews"</span><span class="tag">]</span></code></pre>
</div>
