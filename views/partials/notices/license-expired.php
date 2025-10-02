<?php defined('ABSPATH') || exit; ?>

<?php
    printf(
        _x('One or more of your licenses have expired. %s to enable plugin updates and priority support.', 'Renew your license (admin-text)', 'site-reviews'),
        glsr_premium_link('license-keys', _x('Renew your license', 'admin-text', 'site-reviews'))
    );
?>
<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
