<?php defined('ABSPATH') || exit; ?>

<?php if (glsr()->hasPermission('settings')): ?>
<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="tools-import-plugin-settings">
            <span class="title dashicons-before dashicons-admin-tools"><?= _x('Import Settings', 'admin-text', 'site-reviews'); ?></span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="tools-import-plugin-settings" class="inside">
        <p><?= sprintf(
            _x('Here you can import the Site Reviews settings from a %s file. You can use the the %sExport Settings%s tool to export these settings from another website.', 'admin-text', 'site-reviews'),
            '<code>*.json</code>',
            '<a data-expand="#tools-export-plugin-settings" href="'.glsr_admin_url('tools', 'general').'">', '</a>'
        ); ?></p>
        <form method="post" enctype="multipart/form-data" onsubmit="submit.disabled = true;">
            <?php wp_nonce_field('import-settings'); ?>
            <input type="hidden" name="{{ id }}[_action]" value="import-settings">
            <p>
                <input type="file" name="import-file" accept="application/json">
            </p>
            <button type="submit" class="glsr-button components-button is-primary"
                data-expand="#tools-import-plugin-settings"
                data-loading="<?= esc_attr_x('Importing settings, please wait...', 'admin-text', 'site-reviews'); ?>"
            ><?= _x('Import Settings', 'admin-text', 'site-reviews'); ?>
            </button>
        </form>
    </div>
</div>
<?php endif; ?>
