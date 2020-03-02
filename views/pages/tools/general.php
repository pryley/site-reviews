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
            <button type="submit" class="glsr-button button" name="migrate-plugin" id="migrate-plugin">
                <span data-loading="<?= esc_attr_x('Migrating, please wait...', 'admin-text', 'site-reviews'); ?>"><?= _x('Run Migration', 'admin-text', 'site-reviews'); ?></span>
            </button>
        </p>
    </form>
</div>
<?php endif; ?>

<div class="glsr-card card">
    <h3>Recalculate Summary Counts</h3>
    <p>Site Reviews maintains an internal rating count of your reviews, this allows the plugin to calculate the average rating scores for the summary without negatively impacting performance when you have a lot of reviews.</p>
    <p>If you suspect that the rating counts are incorrect (perhaps you have cloned a page that had reviews assigned to it, or edited/deleted reviews directly from your database), you can recalculate them here.</p>
    <form method="post">
        <input type="hidden" name="{{ id }}[_action]" value="count-reviews">
        <?php wp_nonce_field('count-reviews'); ?>
        <p class="submit">
            <button type="submit" class="glsr-button button" name="count-reviews" id="count-reviews">
                <span data-loading="<?= esc_attr_x('Recalculating Counts, please wait...', 'admin-text', 'site-reviews'); ?>"><?= _x('Recalculate Counts', 'admin-text', 'site-reviews'); ?></span>
            </button>
        </p>
    </form>
</div>

<?php if (glsr()->hasPermission('settings')) : ?>
<div class="glsr-card card">
    <h3>Reset Permissions</h3>
    <p>Site Reviews provides custom post_type capabilities that mirror the capabilities of your posts by default. For example, if a user role has permission to edit others posts, then that role will also have permission to edit other users reviews.</p>
    <p>If you have changed the capabilities of your user roles and you suspect that Site Reviews is not working correctly due to your changes, you may use this tool to reset the Site Reviews capabilities for your user roles.</p>
    <form method="post">
        <input type="hidden" name="{{ id }}[_action]" value="reset-permissions">
        <?php wp_nonce_field('reset-permissions'); ?>
        <p class="submit">
            <button type="submit" class="glsr-button button" name="reset-permissions" id="reset-permissions">
                <span data-loading="<?= esc_attr_x('Resetting Permissions, please wait...', 'admin-text', 'site-reviews'); ?>"><?= _x('Reset Permissions', 'admin-text', 'site-reviews'); ?></span>
            </button>
        </p>
    </form>
</div>
<?php endif; ?>
