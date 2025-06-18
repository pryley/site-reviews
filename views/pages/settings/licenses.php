<?php defined('ABSPATH') || exit; ?>

<h2 class="title"><?php echo _x('Addon Licenses', 'admin-text', 'site-reviews'); ?></h2>

<div class="components-notice is-info" style="margin-left:0;">
    <p class="components-notice__content">
        <?php echo sprintf(
            _x('To authorize a license key to work on your website, go to the %s page on your Nifty Plugins account and click the "Manage Sites" button.', 'link to License Keys page (admin-text)', 'site-reviews'),
            glsr_premium_link('license-keys')
        ); ?>
    </p>
</div>

<table class="form-table">
    <tbody>
        {{ rows }}
    </tbody>
</table>
