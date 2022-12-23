<?php defined('WPINC') || die; ?>

<h2 class="title">{{ title }}</h2>
<div class="components-notice is-info" style="margin-left:0;">
    <p class="components-notice__content">
        <?= _x('The shortcodes entered below <strong>must</strong> include the <code>assigned_posts="post_id"</code> option as that links the reviews to the product.', 'admin-text', 'site-reviews'); ?>
    </p>
</div>
<table class="form-table">
    <tbody>
        {{ rows }}
    </tbody>
</table>
