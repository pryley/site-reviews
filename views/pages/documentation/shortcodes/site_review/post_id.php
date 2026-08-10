<?php defined('ABSPATH') || exit; ?>

<p class="glsr-heading">post_id</p>
<?php echo wp_get_admin_notice(
    'If a Post ID is not provided, the shortcode will display the latest review.',
    ['type' => 'info', 'additional_classes' => ['inline']]
); ?>
<p>Use the "post_id" option to display a review. Accepted value is a numerical Post ID of a review.</p>
<p>The default post_id value is: <code>0</code></p>
<div class="shortcode-example">
    <pre><code class="language-shortcode">[site_review post_id="13"]</code></pre>
</div>
