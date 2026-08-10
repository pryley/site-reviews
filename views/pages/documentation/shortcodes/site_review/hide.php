<?php defined('ABSPATH') || exit; ?>

<p class="glsr-heading">hide</p>
<?php if (glsr_addon_required('site-reviews-images')) { ?>
    <?php echo wp_get_admin_notice(
        glsr_premium_link('site-reviews-images').' addon is required to use the "images" value in this shortcode option.',
        ['type' => 'warning', 'additional_classes' => ['inline']]
    ); ?>
<?php } ?>
<p>Include the "hide" option to hide any specific fields you don't want to show. If all fields are hidden, the shortcode will not be displayed.</p>
<div class="shortcode-example">
    <pre><code class="language-shortcode">[site_reviews hide="assigned_links,author,avatar,content,date,images,rating,response,title"]</code></pre>
</div>
