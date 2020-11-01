<?php defined('ABSPATH') || die; ?>

<?php if (glsr()->hasPermission('settings')) : ?>

<div class="glsr-card card">
    <h3>Export Plugin Settings</h3>
    <p>Export the Site Reviews settings for this site to a <code>*.json</code> file. This allows you to easily import the plugin settings into another site.</p>
    <p>To export your Site Reviews' reviews and categories, please use the WordPress <a href="<?= admin_url('export.php'); ?>">Export</a> tool.</p>
    <form method="post">
        <input type="hidden" name="{{ id }}[_action]" value="export-settings">
        <?php wp_nonce_field('export-settings'); ?>
        <?php submit_button(_x('Export Settings', 'admin-text', 'site-reviews'), 'secondary'); ?>
    </form>
</div>

<div class="glsr-card card">
    <h3>Import Plugin Settings</h3>
    <p>Import the Site Reviews settings from a <code>*.json</code> file. This file can be obtained by exporting the settings on another site using the export tool below.</p>
    <p>To import your Site Reviews' reviews and categories from another website, please use the WordPress <a href="<?= admin_url('import.php'); ?>">Import</a> tool.</p>
    <form method="post" enctype="multipart/form-data" onsubmit="submit.disabled = true;">
        <input type="file" name="import-file" accept="application/json">
        <input type="hidden" name="{{ id }}[_action]" value="import-settings">
        <?php wp_nonce_field('import-settings'); ?>
        <p class="submit">
            <button type="submit" class="glsr-button button" name="submit" id="import-settings">
                <span data-loading="<?= esc_attr_x('Importing settings, please wait...', 'admin-text', 'site-reviews'); ?>"><?= _x('Import Settings', 'admin-text', 'site-reviews'); ?></span>
            </button>
        </p>
    </form>
</div>

<div class="glsr-card card">
    <h3>Import Third Party Reviews</h3>
    <div class="components-notice is-warning">
        <p class="components-notice__content">Please backup your database before running this tool! You can use the <a href="https://wordpress.org/plugins/updraftplus/">UpdraftPlus</a> plugin to do this.</p>
    </div>
    <p>Here you can import third party reviews from a <code>*.CSV</code> file. The CSV file should include a header row, use a comma as the delimiter, and may contain the following columns:</p>
    <p>
        <code>avatar</code> The avatar URL of the reviewer<br>
        <code>content</code> The review (<span class="required">required</span>)<br>
        <code>date</code> The review date as <span class="code">yyyy-mm-dd</span> or a timestamp (<span class="required">required</span>)<br>
        <code>email</code> The reviewer's email<br>
        <code>ip_address</code> The IP address of the reviewer<br>
        <code>is_pinned</code> True or false<br>
        <code>name</code> The reviewer's name<br>
        <code>rating</code> A number from 0-<?= glsr()->constant('MAX_RATING', 'GeminiLabs\SiteReviews\Modules\Rating'); ?> (<span class="required">required</span>)<br>
        <code>response</code> The review response<br>
        <code>title</code> The title of the review<br>
    </p>
    <p>Entries in the CSV file that do not contain required values will be skipped.</p>
    <form method="post" enctype="multipart/form-data" onsubmit="submit.disabled = true;">
        <input type="file" name="import-file" accept="text/csv">
        <input type="hidden" name="{{ id }}[_action]" value="import-reviews">
        <?php wp_nonce_field('import-reviews'); ?>
        <p class="submit">
            <button type="submit" class="glsr-button button" name="submit" id="import-reviews">
                <span data-loading="<?= esc_attr_x('Importing reviews, please wait...', 'admin-text', 'site-reviews'); ?>"><?= _x('Import Reviews', 'admin-text', 'site-reviews'); ?></span>
            </button>
        </p>
    </form>
</div>

<div class="glsr-card card">
    <h3>Migrate Plugin</h3>
    <div class="components-notice is-info">
        <p class="components-notice__content">Hold down the ALT/Option key to force-run all previous migrations.</p>
    </div>
    <p>Run this tool if your reviews stopped working correctly after upgrading the plugin to the latest version (i.e. read-only reviews, zero-star ratings, missing role capabilities, etc.).</p>
    <form method="post">
        <input type="hidden" name="{{ id }}[_action]" value="migrate-plugin">
        <input type="hidden" name="{{ id }}[alt]" value="0" data-alt>
        <?php wp_nonce_field('migrate-plugin'); ?>
        <p class="submit">
            <button type="submit" class="glsr-button button" name="migrate-plugin" id="migrate-plugin" data-ajax-click data-remove-notice="migrate">
                <span data-alt-text="<?= esc_attr_x('Run All Migrations', 'admin-text', 'site-reviews'); ?>" data-loading="<?= esc_attr_x('Migrating, please wait...', 'admin-text', 'site-reviews'); ?>"><?= _x('Run Migration', 'admin-text', 'site-reviews'); ?></span>
            </button>
        </p>
    </form>
</div>

<div class="glsr-card card">
    <h3>Reset Assigned Meta Values</h3>
    <p>Site Reviews stores the individual review count, average rating, and ranking for each assigned post, category, and user. If you suspect that these meta values are incorrect (perhaps you cloned a page that had reviews assigned to it), you may use this tool to recalculate them.</p>
    <form method="post">
        <input type="hidden" name="{{ id }}[_action]" value="reset-assigned-meta">
        <?php wp_nonce_field('reset-assigned-meta'); ?>
        <p class="submit">
            <button type="submit" class="glsr-button button" name="reset-assigned-meta" id="reset-assigned-meta" data-ajax-click>
                <span data-loading="<?= esc_attr_x('Resetting values, please wait...', 'admin-text', 'site-reviews'); ?>"><?= _x('Reset Meta Values', 'admin-text', 'site-reviews'); ?></span>
            </button>
        </p>
    </form>
</div>

<div class="glsr-card card">
    <h3>Reset Permissions</h3>
    <div class="components-notice is-info">
        <p class="components-notice__content">Hold down the ALT/Option key to perform a "Hard Reset"; this removes all Site Reviews capabilites from your Editor, Author, and Contributor roles before re-adding them.</p>
    </div>
    <p>Site Reviews provides custom post_type capabilities that mirror the capabilities of your posts by default. For example, if a user role has permission to edit others posts, then that role will also have permission to edit other users reviews.</p>
    <p>If you have changed the capabilities of your user roles (Administrator, Editor, Author, and Contributor) and you suspect that Site Reviews is not working correctly due to your changes, you may use this tool to reset the Site Reviews capabilities for your user roles.</p>
    <form method="post">
        <input type="hidden" name="{{ id }}[_action]" value="reset-permissions">
        <input type="hidden" name="{{ id }}[alt]" value="0" data-alt>
        <?php wp_nonce_field('reset-permissions'); ?>
        <p class="submit">
            <button type="submit" class="glsr-button button" name="reset-permissions" id="reset-permissions" data-ajax-click>
                <span data-alt-text="<?= esc_attr_x('Hard Reset Permissions', 'admin-text', 'site-reviews'); ?>" data-loading="<?= esc_attr_x('Resetting, please wait...', 'admin-text', 'site-reviews'); ?>"><?= _x('Reset Permissions', 'admin-text', 'site-reviews'); ?></span>
            </button>
        </p>
    </form>
</div>

<?php endif; ?>

<div class="glsr-card card">
    <h3>Test IP Address Detection</h3>
    <p>When reviews are submitted on your website, Site Reviews detects the IP address of the person submitting the review and saves it to the submitted review. This allows you to limit review submissions or to blacklist reviewers based on their IP address. The IP address is also used by Akismet (if you have enabled the integration) to catch spam submissions.</p>
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
