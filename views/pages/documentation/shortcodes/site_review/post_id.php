<?php defined('ABSPATH') || exit; ?>

<p class="glsr-heading">post_id</p>
<div class="glsr-notice-inline components-notice is-warning">
    <p class="components-notice__content">If a Post ID is not provided, the shortcode will display the latest review.</p>
</div>
<p>Use the "post_id" option to display a review. Accepted value is a numerical Post ID of a review.</p>
<p>The default post_id value is: <code>0</code></p>
<div class="shortcode-example">
    <input type="text" readonly class="code" value='[site_review post_id="13"]'>
    <pre><code class="syntax-shortcode"><span class="tag">[site_review</span> <span class="attr-name">post_id</span>=<span class="attr-value">"13"</span><span class="tag">]</span></code></pre>
</div>
