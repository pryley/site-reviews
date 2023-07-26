<?php defined('ABSPATH') || exit; ?>

<?php if (glsr()->hasPermission('settings')): ?>
<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="tools-migrate-plugin">
            <span class="title dashicons-before dashicons-admin-tools"><?= _x('Migrate Plugin', 'admin-text', 'site-reviews'); ?></span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="tools-migrate-plugin" class="inside">
        <p><?= _x('Run this tool if your reviews stopped working correctly after upgrading the plugin to the latest version (i.e. read-only reviews, zero-star ratings, missing role capabilities, etc.).', 'admin-text', 'site-reviews'); ?></p>
        <p><?= _x('If Site Reviews stopped working after cloning your website or after restoring your website from a backup, click the <strong>Hard Reset</strong> button.', 'admin-text', 'site-reviews'); ?></p>
        <form method="post">
            <?php wp_nonce_field('migrate-plugin'); ?>
            <input type="hidden" name="{{ id }}[_action]" value="migrate-plugin">
            <input type="hidden" name="{{ id }}[alt]" value="0" data-alt>
            <button type="submit" class="glsr-button components-button is-primary" 
                data-ajax-click
                data-ajax-scroll
                data-alt
                data-loading="<?= esc_attr_x('Migrating, please wait...', 'admin-text', 'site-reviews'); ?>"
                data-remove-notice="migrate"
            ><?= _x('Migrate Plugin', 'admin-text', 'site-reviews'); ?>
            </button>
            <button type="submit" class="glsr-button components-button is-secondary" 
                data-ajax-click
                data-ajax-scroll
                data-alt
                data-loading="<?= esc_attr_x('Migrating, please wait...', 'admin-text', 'site-reviews'); ?>"
                data-remove-notice="migrate"
            ><?= _x('Hard Reset', 'admin-text', 'site-reviews'); ?>
            </button>
        </form>
    </div>
</div>
<?php endif; ?>
