<?php defined('ABSPATH') || exit;

echo wp_get_admin_notice(
    /* translators: %s: link with the text "login" */
    sprintf(_x('To receive support for this addon, please %s to your Nifty Plugins account.', 'login (admin-text)', 'site-reviews'),
        glsr_premium_link('account', _x('login', 'admin-text', 'site-reviews'))
    ),
    [
        'type' => 'info',
        'additional_classes' => ['inline'],
    ]
);
