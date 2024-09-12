<?php defined('ABSPATH') || exit; ?>

<?php if (glsr()->hasPermission('settings')): ?>
<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="tools-export-plugin-settings">
            <span class="title dashicons-before dashicons-admin-tools"><?php echo _x('Export Settings', 'admin-text', 'site-reviews'); ?></span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="tools-export-plugin-settings" class="inside">
        <p><?php echo sprintf(
            _x('Here you can export your Site Reviews settings to a %s file. You can use the the %sImport Settings%s tool to import the settings on another website.', 'admin-text', 'site-reviews'),
            '<code>*.json</code>',
            '<a data-expand="#tools-import-plugin-settings" href="'.glsr_admin_url('tools', 'general').'">', '</a>'
        ); ?></p>
        <form method="post">
            <?php wp_nonce_field('export-settings'); ?>
            <input type="hidden" name="{{ id }}[_action]" value="export-settings">
            <button type="submit" class="glsr-button button button-large button-primary">
                <?php echo _x('Export Settings', 'admin-text', 'site-reviews'); ?>
            </button>
        </form>
    </div>
</div>
<?php endif; ?>
