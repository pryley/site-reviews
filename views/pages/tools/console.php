<?php defined('ABSPATH') || die; ?>

<textarea id="glsr-log-file" class="large-text code glsr-code glsr-code-large" rows="20" readonly>{{ console }}</textarea>
<form method="post" class="glsr-float-left">
    <input type="hidden" name="{{ id }}[_action]" value="download-console">
    <?php wp_nonce_field('download-console'); ?>
    <button type="submit" id="glsr-download-console" class="components-button is-primary">
        <?= _x('Download Console', 'admin-text', 'site-reviews'); ?>
    </button>
</form>
<form method="post" class="glsr-float-left">
    <input type="hidden" name="{{ id }}[_action]" value="fetch-console">
    <?php wp_nonce_field('fetch-console'); ?>
    <button type="submit" id="glsr-fetch-console" class="glsr-button components-button is-secondary">
        <span data-loading="<?= esc_attr_x('Reloading...', 'admin-text', 'site-reviews'); ?>"><?= _x('Reload', 'admin-text', 'site-reviews'); ?></span>
    </button>
</form>
<form method="post">
    <input type="hidden" name="{{ id }}[_action]" value="clear-console">
    <?php wp_nonce_field('clear-console'); ?>
    <button type="submit" id="glsr-clear-console" class="glsr-button components-button is-secondary">
        <span data-loading="<?= esc_attr_x('Clearing...', 'admin-text', 'site-reviews'); ?>"><?= _x('Clear', 'admin-text', 'site-reviews'); ?></span>
    </button>
</form>
