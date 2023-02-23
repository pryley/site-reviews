<?php defined('ABSPATH') || exit; ?>

<p class="glsr-heading">assigned_posts</p>
<p>Include the "assigned_posts" option to automatically assign submitted reviews to specific posts, pages, or other public post types. Accepted values are a <a href="https://wpklik.com/wordpress-tutorials/how-to-quickly-find-your-wordpress-page-and-post-id/" target="_blank">WordPress Post ID</a>, <code>post_id</code> which automatically uses the Post ID of the current page, <code>parent_id</code> which automatically uses the Post ID of the parent page, or the page slug entered in the format of <code>post_type:slug</code>. Separate multiple values with a comma.</p>
<div class="shortcode-example">
    <pre><code class="language-shortcode">[site_reviews_form assigned_posts="post_id"]</code></pre>
</div>
