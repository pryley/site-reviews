<?php defined('ABSPATH') || exit; ?>

<p class="glsr-heading">assigned_posts</p>
<p>Include the "assigned_posts" option to limit reviews to those assigned to specific posts, pages, or other public post types. Accepted values are a <a href="https://wpklik.com/wordpress-tutorials/how-to-quickly-find-your-wordpress-page-and-post-id/" target="_blank">WordPress Post ID</a>, <code>post_id</code> which automatically uses the Post ID of the current page, <code>parent_id</code> which automatically uses the Post ID of the parent page, the page slug entered in the format of <code>post_type:slug</code>, or a public Post Type. If you enter a Post Type, it will override all other entered values. Separate multiple values with a comma.</p>
<p><span class="required">Important:</span> If you are using this shortcode together with the [site_reviews_summary] shortcode, make sure to set the same option value for both shortcodes.</p>
<div class="shortcode-example">
    <pre><code class="language-shortcode">[site_reviews assigned_posts="post_id"]</code></pre>
</div>
