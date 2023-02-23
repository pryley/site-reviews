<?php defined('ABSPATH') || exit; ?>

<p class="glsr-heading">assigned_users</p>
<p>Include the "assigned_users" option to limit reviews to those assigned to specific users. Accepted values are a <a href="https://wpklik.com/wordpress-tutorials/wordpress-user-id/" target="_blank">WordPress User ID</a>, username, <code>author_id</code> which automatically uses the User ID of the author of the current page, <code>profile_id</code> which automatically uses the User ID of the BuddyPress or Ultimate Member profile page, or <code>user_id</code> which automatically uses the User ID of the logged in user.  Separate multiple values with a comma.</p>
<p><span class="required">Important:</span> If you are using this shortcode together with the [site_reviews_summary] shortcode, make sure to set the same option value for both shortcodes.</p>
<div class="shortcode-example">
    <pre><code class="language-shortcode">[site_reviews assigned_users="user_id"]</code></pre>
</div>
