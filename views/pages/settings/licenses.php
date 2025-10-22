<?php defined('ABSPATH') || exit; ?>

<h2 class="title"><?php echo _x('Addon Licenses', 'admin-text', 'site-reviews'); ?></h2>

<div class="components-notice is-info" style="margin-left:0;">
    <p class="components-notice__content">
        <?php echo sprintf(
            _x('To change the website associated with your license key, go to the %s page and click the "Manage Sites" button.', 'link to License Keys page (admin-text)', 'site-reviews'),
            glsr_premium_link('license-keys')
        ); ?>
    </p>
</div>

<table class="form-table">
    <tbody>
        {{ rows }}
    </tbody>
</table>
