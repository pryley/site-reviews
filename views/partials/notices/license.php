<?php defined('ABSPATH') || exit; ?>

<div class="glsr-notice glsr-notice-top-of-page" data-dismiss="license">
<?php
    if (!$licensed) {
        printf(_x('You are using the free version of Site Reviews. %sPurchase premium%s to support future development and get images, filters, themes, custom forms, and more!', 'admin-text', 'site-reviews'),
            '<a href="https://niftyplugins.com/plugins/site-reviews-premium/" target="_blank">',
            '</a>'
        );
    } elseif ($expired) {
        printf(
            _x('One or more of your licenses have expired. %sRenew your license%s to get the latest updates and priority support.', 'admin-text', 'site-reviews'),
            '<a href="https://niftyplugins.com/account/license-keys/" target="_blank">',
            '</a>'
        );
    } else {
        printf(
            _x('One or more addons are not receiving updates. %sSave your license%s in the settings to get the latest updates and priority support.', 'admin-text', 'site-reviews'),
            '<a href="'.glsr_admin_url('settings', 'licenses').'">',
            '</a>'
        );
    }
?>
    <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
</div>
