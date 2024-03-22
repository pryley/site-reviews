<?php defined('ABSPATH') || exit; ?>

<p class="glsr-heading">hide</p>
<div class="glsr-notice-inline components-notice is-warning">
    <p class="components-notice__content">The <a href="<?php echo glsr_admin_url('addons'); ?>">Review Images</a> addon is required to use the "images" value in this shortcode option.</p>
</div>
<p>Include the "hide" option to hide any specific fields you don't want to show. If all fields are hidden, the shortcode will not be displayed.</p>
<div class="shortcode-example">
    <pre><code class="language-shortcode">[site_reviews hide="assigned_links,author,avatar,content,date,images,rating,response,title"]</code></pre>
</div>
