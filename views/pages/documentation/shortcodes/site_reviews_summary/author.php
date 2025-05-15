<?php defined('ABSPATH') || exit; ?>

<p class="glsr-heading">author</p>
<p>Include the "author" option to limit reviews to those authored by a specific user. Accepted values are a <a href="https://wpklik.com/wordpress-tutorials/wordpress-user-id/" target="_blank">WordPress User ID</a>, username, or <code>user_id</code> which automatically uses the User ID of the logged in user.</p>
<p><span class="required">Important:</span> If you are using this shortcode together with the [site_reviews] shortcode, make sure to set the same option value for both shortcodes.</p>
<div class="shortcode-example">
    <pre><code class="language-shortcode">[site_reviews_summary author="user_id"]</code></pre>
</div>
