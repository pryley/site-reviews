<?php defined('ABSPATH') || exit; ?>

<p class="glsr-heading">rating_field</p>
<?php if (glsr_addon_required('site-reviews-forms')) { ?>
    <?php echo wp_get_admin_notice(
        glsr_premium_link('site-reviews-forms').' addon is required to use this shortcode option.',
        ['type' => 'warning', 'additional_classes' => ['inline']]
    ); ?>
<?php } ?>
<p>Include the "rating_field" option to make the "rating" option apply to the value of a custom rating field. Use the custom rating Field Name as the value.</p>
<div class="shortcode-example">
    <pre><code class="language-shortcode">[site_reviews rating_field="sound_rating"]</code></pre>
</div>
