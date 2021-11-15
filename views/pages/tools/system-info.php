<?php defined('ABSPATH') || die; ?>

<form method="post">
    <textarea class="large-text code glsr-code glsr-code-large" name="{{ id }}[system-info]" rows="20" onclick="this.select()" readonly>{{ system }}</textarea>
    <input type="hidden" name="{{ id }}[_action]" value="download-system-info">
    <?php wp_nonce_field('download-system-info'); ?>
    <button type="submit" id="glsr-download-system-info" class="components-button is-primary">
        <?= _x('Download System Info', 'admin-text', 'site-reviews'); ?>
    </button>
</form>
