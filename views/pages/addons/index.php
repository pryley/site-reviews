<?php defined('ABSPATH') || exit; ?>

<div class="wrap">
    <hr class="wp-header-end" />
    <?= $notices; ?>
    <div class="glsr-premium-hero">
        <div class="glsr-premium-hero-image"></div>
        <div class="glsr-premium-hero-content">
            <h2><?= _x('Site Reviews Premium', 'admin-text', 'site-reviews'); ?></h2>
            <p>
                <?= sprintf(_x('Gain access to all of our addons with Site Reviews Premium, including access to future addons as they are released and <a href="%s" target="_blank">priority support</a>!', 'admin-text', 'site-reviews'),
                    'https://niftyplugins.com/account/support/'
                ); ?>
            </p>
            <a href="https://niftyplugins.com/plugins/site-reviews-premium/" class="button button-hero button-primary" target="_blank"><?= _x('Check it out!', 'admin-text', 'site-reviews'); ?></a>
        </div>
    </div>

    <div class="glsr-addons">
        <?php $template->renderMultiple('partials/addons/addon', $addons); ?>
    </div>
</div>
