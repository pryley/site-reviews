<?php defined('ABSPATH') || exit; ?>

<p class="glsr-heading">rating_field</p>
<div class="glsr-notice-inline components-notice is-info">
    <p class="components-notice__content">Custom rating fields can be added with the <a href="<?= glsr_admin_url('addons'); ?>">Review Forms</a> addon.</p>
</div>
<p>Include the "rating_field" option to make the "rating" option apply to the value of a custom rating field. Use the custom rating Field Name as the value.</p>
<div class="shortcode-example">
    <pre><code class="language-shortcode">[site_reviews rating_field="sound_rating"]</code></pre>
</div>
