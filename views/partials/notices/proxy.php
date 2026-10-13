<?php defined('ABSPATH') || exit;

$tool = glsr_admin_link('tools.general', _x('Configure IP Address Detection', 'admin-text', 'site-reviews'), '#tools-ip-detection');

?>

<p>
    <?php if (empty($header)) { ?>
        <?php /* translators: %s: link with the text "Configure IP Address Detection" */ printf(
            _x('Site Reviews is ignoring the proxy HTTP header added by a code snippet because no trusted proxies have been entered. To fix it, remove the code snippet, then select the Proxy HTTP Header and enter your trusted proxies in the %s tool.', 'admin-text', 'site-reviews'),
            $tool
        ); ?>
    <?php } else { ?>
        <?php /* translators: %1$s: the name of the HTTP header, %2$s: link with the text "Configure IP Address Detection" */ printf(
            _x('Site Reviews is ignoring the %1$s proxy HTTP header because no trusted proxies have been entered. Enter your trusted proxies in the %2$s tool, or set the Proxy HTTP Header to None.', 'admin-text', 'site-reviews'),
            '<code>'.esc_html($header).'</code>',
            $tool
        ); ?>
    <?php } ?>
</p>
