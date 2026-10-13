<?php defined('ABSPATH') || exit; ?>

<p>
    <?php /* translators: %s: link with the text "Configure IP Address Detection" */ printf(
        _x('A previous version of Site Reviews did not save every line of the Trusted Proxies list. Please check your trusted proxies in the %s tool and save them again.', 'admin-text', 'site-reviews'),
        glsr_admin_link('tools.general', _x('Configure IP Address Detection', 'admin-text', 'site-reviews'), '#tools-ip-detection')
    ); ?>
</p>
