<?php defined('ABSPATH') || exit;

$system = glsr('Modules\SystemInfo')->get();

?>
<form method="post">
    <?php wp_nonce_field('download-system-info'); ?>
    <textarea class="large-text code glsr-code glsr-code-large" name="{{ id }}[system-info]" rows="20" readonly><?= esc_html($system); ?></textarea>
    <input type="hidden" name="{{ id }}[_action]" value="download-system-info">
    <button type="submit" id="glsr-download-system-info" class="button button-primary" style="margin-top: 4px;">
        <?= _x('Download System Info', 'admin-text', 'site-reviews'); ?>
    </button>
</form>
