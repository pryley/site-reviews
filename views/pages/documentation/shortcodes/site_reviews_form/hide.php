<?php defined('ABSPATH') || exit; ?>

<p class="glsr-heading">hide</p>
<div class="glsr-notice-inline components-notice is-warning">
    <p class="components-notice__content">The <a href="<?php echo glsr_admin_url('addons'); ?>">Review Images</a> addon is required to use the "images" value in this shortcode option.</p>
</div>
<p>Include the "hide" option to exclude certain fields in the form. If all fields are hidden, the shortcode will not be displayed.</p>
<div class="shortcode-example">
    <pre><code class="language-shortcode">[site_reviews_form hide="content,email,images,name,rating,terms,title"]</code></pre>
</div>
