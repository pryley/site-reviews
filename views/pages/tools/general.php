<?php defined('WPINC') || die; ?>

<?php if (glsr()->hasPermission('settings')) : ?>
<div class="glsr-card card">
    <h3>Export Settings</h3>
    <p>Export the Site Reviews settings for this site to a <code>*.json</code> file. This allows you to easily import the plugin settings into another site.</p>
    <p>To export your Site Reviews' reviews and categories, please use the WordPress <a href="<?= admin_url('export.php'); ?>">Export</a> tool.</p>
    <form method="post">
        <input type="hidden" name="{{ id }}[_action]" value="export-settings">
        <?php wp_nonce_field('export-settings'); ?>
        <?php submit_button(_x('Export Settings', 'admin-text', 'site-reviews'), 'secondary'); ?>
    </form>
</div>
<?php endif; ?>

<?php if (glsr()->hasPermission('settings')) : ?>
<div class="glsr-card card">
    <h3>Import Settings</h3>
    <p>Import the Site Reviews settings from a <code>*.json</code> file. This file can be obtained by exporting the settings on another site using the export tool below.</p>
    <p>To import your Site Reviews' reviews and categories from another website, please use the WordPress <a href="<?= admin_url('import.php'); ?>">Import</a> tool.</p>
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="import-file">
        <input type="hidden" name="{{ id }}[_action]" value="import-settings">
        <?php wp_nonce_field('import-settings'); ?>
        <?php submit_button(_x('Import Settings', 'admin-text', 'site-reviews'), 'secondary'); ?>
    </form>
</div>
<?php endif; ?>

<?php if (glsr()->hasPermission('settings')) : ?>
<div class="glsr-card card">
    <h3>Migrate Plugin</h3>
    <p>Run this tool if your reviews stopped working correctly after upgrading the plugin to the latest version (i.e. read-only reviews, zero-star ratings, missing role capabilities, etc.).</p>
    <form method="post">
        <input type="hidden" name="{{ id }}[_action]" value="migrate-plugin">
        <?php wp_nonce_field('migrate-plugin'); ?>
        <p class="submit">
            <button type="submit" class="glsr-button button" name="migrate-plugin" id="migrate-plugin" data-ajax-click>
                <span data-loading="<?= esc_attr_x('Migrating, please wait...', 'admin-text', 'site-reviews'); ?>"><?= _x('Run Migration', 'admin-text', 'site-reviews'); ?></span>
            </button>
        </p>
    </form>
</div>
<?php endif; ?>

<?php if (glsr()->hasPermission('settings')) : ?>
<div class="glsr-card card">
    <h3>Reset Permissions</h3>
    <p>Site Reviews provides custom post_type capabilities that mirror the capabilities of your posts by default. For example, if a user role has permission to edit others posts, then that role will also have permission to edit other users reviews.</p>
    <p>If you have changed the capabilities of your user roles and you suspect that Site Reviews is not working correctly due to your changes, you may use this tool to reset the Site Reviews capabilities for your user roles.</p>
    <form method="post">
        <input type="hidden" name="{{ id }}[_action]" value="reset-permissions">
        <?php wp_nonce_field('reset-permissions'); ?>
        <p class="submit">
            <button type="submit" class="glsr-button button" name="reset-permissions" id="reset-permissions" data-ajax-click>
                <span data-loading="<?= esc_attr_x('Resetting, please wait...', 'admin-text', 'site-reviews'); ?>"><?= _x('Reset Permissions', 'admin-text', 'site-reviews'); ?></span>
            </button>
        </p>
    </form>
</div>
<?php endif; ?>

<div class="glsr-card card">
    <h3>Test IP Address Detection</h3>
    <p>When reviews are submitted on your website, Site Reviews detects the IP address of the reviewer and saves it to the submitted review. This allows you to limit review submissions or to blacklist reviewers based on their IP address. The IP address is also used by Akismet (if you have enabled the integration) to catch spam submissions.</p>
    <p>If you are getting an "unknown" value for IP addresses in your reviews, you may use this tool to check the visitor IP address detection.</p>
    <form method="post">
        <input type="hidden" name="{{ id }}[_action]" value="detect-ip-address">
        <?php wp_nonce_field('detect-ip-address'); ?>
        <p class="submit">
            <button type="submit" class="glsr-button button" name="detect-ip-address" id="detect-ip-address" data-ajax-click>
                <span data-loading="<?= esc_attr_x('Testing, please wait...', 'admin-text', 'site-reviews'); ?>"><?= _x('Test Detection', 'admin-text', 'site-reviews'); ?></span>
            </button>
        </p>
    </form>
</div>
