<?php defined('ABSPATH') || exit; ?>

<form method="post">
    <?php wp_nonce_field('download-system-info'); ?>
    <textarea class="large-text code glsr-code glsr-code-large" name="{{ id }}[system-info]" rows="20" readonly><?php _ex('Loading, please wait...', 'admin-text', 'site-reviews'); ?></textarea>
    <input type="hidden" name="{{ id }}[_action]" value="download-system-info">
    <button disabled type="submit" id="glsr-download-system-info" class="button button-primary" style="margin-top: 4px;">
        <?php echo _x('Download System Info', 'admin-text', 'site-reviews'); ?>
    </button>
</form>
