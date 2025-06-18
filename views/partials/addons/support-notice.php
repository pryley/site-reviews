<?php defined('ABSPATH') || exit; ?>

<div class="glsr-notice-inline components-notice is-info">
    <p class="components-notice__content">
        <?php echo sprintf(_x('To receive support for this addon, please %s to your Nifty Plugins account.', 'login (admin-text)', 'site-reviews'),
            glsr_premium_link('account', _x('login', 'admin-text', 'site-reviews'))
        ); ?>
    </p>
</div>
