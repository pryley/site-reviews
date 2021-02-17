<?php defined('ABSPATH') || exit; ?>

<?php if (glsr()->hasPermission('settings')) { ?>

<div class="glsr-card card">
    <h3><?= _x('Export Plugin Settings', 'admin-text', 'site-reviews'); ?></h3>
    <p><?= sprintf(
        _x('Export the Site Reviews settings for this site to a %s file. This allows you to easily import the plugin settings into another site.', 'admin-text', 'site-reviews'),
        '<code>*.json</code>'
    ); ?></p>
    <p><?= sprintf(
        _x('To export your reviews and categories, please use the WordPress %s tool.', 'admin-text', 'site-reviews'),
        sprintf('<a href="%s">%s</a>', admin_url('export.php'), _x('Export', 'admin-text', 'site-reviews'))
    ); ?></p>
    <form method="post">
        <?php wp_nonce_field('export-settings'); ?>
        <input type="hidden" name="{{ id }}[_action]" value="export-settings">
        <?php submit_button(_x('Export Settings', 'admin-text', 'site-reviews'), 'secondary'); ?>
    </form>
</div>

<div class="glsr-card card">
    <h3><?= _x('Import Plugin Settings', 'admin-text', 'site-reviews'); ?></h3>
    <p><?= sprintf(
        _x('Import the Site Reviews settings from a %s file. This file can be obtained by exporting the settings on another site using the export tool below.', 'admin-text', 'site-reviews'),
        '<code>*.json</code>'
    ); ?></p>
    <p><?= sprintf(
        _x('To import your reviews and categories from another website, please use the WordPress %s tool.', 'admin-text', 'site-reviews'),
        sprintf('<a href="%s">%s</a>', admin_url('import.php'), _x('Import', 'admin-text', 'site-reviews'))
    ); ?></p>
    <form method="post" enctype="multipart/form-data" onsubmit="submit.disabled = true;">
        <?php wp_nonce_field('import-settings'); ?>
        <input type="file" name="import-file" accept="application/json">
        <input type="hidden" name="{{ id }}[_action]" value="import-settings">
        <p class="submit">
            <button type="submit" class="glsr-button button" name="submit" id="import-settings">
                <span data-loading="<?= esc_attr_x('Importing settings, please wait...', 'admin-text', 'site-reviews'); ?>"><?= _x('Import Settings', 'admin-text', 'site-reviews'); ?></span>
            </button>
        </p>
    </form>
</div>

<div class="glsr-card card">
    <h3><?= _x('Import Third Party Reviews', 'admin-text', 'site-reviews'); ?></h3>
    <div class="components-notice is-warning">
        <p class="components-notice__content"><?= sprintf(
            _x('Please backup your database before running this tool! You can use the %s plugin to do this.', 'admin-text', 'site-reviews'),
            '<a href="https://wordpress.org/plugins/updraftplus/">UpdraftPlus</a>'
        ); ?></p>
    </div>
    <p><?= sprintf(
        _x('Here you can import third party reviews from a %s file. The CSV file should include a header row, use a comma as the delimiter, and may contain the following columns:', 'admin-text', 'site-reviews'),
        '<code>*.CSV</code>'
    ); ?></p>
    <p>
        <code>avatar</code> <?= _x('The avatar URL of the reviewer', 'admin-text', 'site-reviews'); ?><br>
        <code>content</code> <?= sprintf('%s (<span class="required">%s</span>)', _x('The review', 'admin-text', 'site-reviews'), _x('required', 'admin-text', 'site-reviews')); ?><br>
        <code>date</code> <?= sprintf('%s (<span class="required">%s</span>)', sprintf(_x('The review date as %s or a timestamp', 'admin-text', 'site-reviews'), '<span class="code"><strong>yyyy-mm-dd</strong></span>'), _x('required', 'admin-text', 'site-reviews')); ?><br>
        <code>email</code> <?= _x('The reviewer\'s email', 'admin-text', 'site-reviews'); ?><br>
        <code>ip_address</code> <?= _x('The IP address of the reviewer', 'admin-text', 'site-reviews'); ?><br>
        <code>is_pinned</code> <?= _x('TRUE or FALSE', 'admin-text', 'site-reviews'); ?><br>
        <code>name</code> <?= _x('The reviewer\'s name', 'admin-text', 'site-reviews'); ?><br>
        <code>rating</code> <?= sprintf('%s (<span class="required">%s</span>)', sprintf(_x('A number from 0-%d', 'admin-text', 'site-reviews'), glsr()->constant('MAX_RATING', 'GeminiLabs\SiteReviews\Modules\Rating')), _x('required', 'admin-text', 'site-reviews')); ?><br>
        <code>response</code> <?= _x('The review response', 'admin-text', 'site-reviews'); ?><br>
        <code>title</code> <?= _x('The title of the review', 'admin-text', 'site-reviews'); ?><br>
    </p>
    <p><?= _x('Entries in the CSV file that do not contain required values will be skipped.', 'admin-text', 'site-reviews'); ?></p>
    <form method="post" enctype="multipart/form-data" onsubmit="submit.disabled = true;">
        <?php wp_nonce_field('import-reviews'); ?>
        <input type="file" name="import-file" accept="text/csv">
        <input type="hidden" name="{{ id }}[_action]" value="import-reviews">
        <p class="submit">
            <button type="submit" class="glsr-button button" name="submit" id="import-reviews">
                <span data-loading="<?= esc_attr_x('Importing reviews, please wait...', 'admin-text', 'site-reviews'); ?>"><?= _x('Import Reviews', 'admin-text', 'site-reviews'); ?></span>
            </button>
        </p>
    </form>
</div>

<div class="glsr-card card">
    <h3><?= _x('Migrate Plugin', 'admin-text', 'site-reviews'); ?></h3>
    <div class="components-notice is-info">
        <p class="components-notice__content"><?= _x('Hold down the ALT/Option key to force-run all previous migrations.', 'admin-text', 'site-reviews'); ?></p>
    </div>
    <p><?= _x('Run this tool if your reviews stopped working correctly after upgrading the plugin to the latest version (i.e. read-only reviews, zero-star ratings, missing role capabilities, etc.).', 'admin-text', 'site-reviews'); ?></p>
    <form method="post">
        <?php wp_nonce_field('migrate-plugin'); ?>
        <input type="hidden" name="{{ id }}[_action]" value="migrate-plugin">
        <input type="hidden" name="{{ id }}[alt]" value="0" data-alt>
        <p class="submit">
            <button type="submit" class="glsr-button button" name="migrate-plugin" id="migrate-plugin" data-ajax-click data-ajax-scroll data-remove-notice="migrate">
                <span data-alt-text="<?= esc_attr_x('Run All Migrations', 'admin-text', 'site-reviews'); ?>" data-loading="<?= esc_attr_x('Migrating, please wait...', 'admin-text', 'site-reviews'); ?>"><?= _x('Run Migration', 'admin-text', 'site-reviews'); ?></span>
            </button>
        </p>
    </form>
</div>

<div class="glsr-card card">
    <h3><?= _x('Optimise Your Database Tables', 'admin-text', 'site-reviews'); ?></h3>
    <?php if (!empty($myisam_tables)) { ?>
        <div class="components-notice is-warning">
            <p class="components-notice__content"><?= sprintf(
                _x('Please backup your database before running this tool! You can use the %s plugin to do this.', 'admin-text', 'site-reviews'),
                '<a href="https://wordpress.org/plugins/updraftplus/">UpdraftPlus</a>'
            ); ?></p>
        </div>
        <p><?= _x('The old MyISAM table engine in MySQL was replaced by the InnoDB engine as the default over 10 years ago! If your database tables still use the MyISAM engine, you are missing out on substantial performance and reliability gains that the InnoDB engine provides.', 'admin-text', 'site-reviews'); ?></p>
        <p><?= _x('Site Reviews makes use of specific InnoDB engine features in order to perform faster database queries. However, some of your database tables (shown below) are still using the old MyISAM engine. If you convert these tables to use the InnoDB engine, it will make Site Reviews perform faster.', 'admin-text', 'site-reviews'); ?></p>
        <table class="wp-list-table widefat striped" style="margin-bottom:1em;">
            <thead>
                <tr>
                    <th scope="col"><strong><?= _x('Table', 'admin-text', 'site-reviews'); ?></strong></th>
                    <th scope="col"><strong><?= _x('Engine', 'admin-text', 'site-reviews'); ?></strong></th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($myisam_tables as $table) { ?>
                <tr data-ajax-hide>
                    <td style="vertical-align:middle;"><?= $table; ?></td>
                    <td style="vertical-align:middle;">MyISAM</td>
                    <td style="text-align:right;">
                        <form method="post">
                            <?php wp_nonce_field('convert-table-engine'); ?>
                            <input type="hidden" name="{{ id }}[_action]" value="convert-table-engine">
                            <input type="hidden" name="{{ id }}[table]" value="<?= $table; ?>">
                            <button type="submit" class="glsr-button glsr-button components-button is-secondary is-small" name="convert-table-engine" data-ajax-click>
                                <span data-loading="<?= esc_attr_x('Converting, please wait...', 'admin-text', 'site-reviews'); ?>"><?= _x('Convert table engine to InnoDB', 'admin-text', 'site-reviews'); ?></span>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <div class="components-notice is-success" style="margin-bottom:1em;">
            <p class="components-notice__content"><?= _x('Optimisation is unnecessary because your database tables already use the InnoDB engine!', 'admin-text', 'site-reviews'); ?> ✨</p>
        </div>
    <?php } ?>
</div>

<div class="glsr-card card">
    <h3><?= _x('Reset Assigned Meta Values', 'admin-text', 'site-reviews'); ?></h3>
    <p><?= _x('Site Reviews stores the individual review count, average rating, and ranking for each assigned post, category, and user. If you suspect that these meta values are incorrect (perhaps you cloned a page that had reviews assigned to it), you may use this tool to recalculate them.', 'admin-text', 'site-reviews'); ?></p>
    <form method="post">
        <?php wp_nonce_field('reset-assigned-meta'); ?>
        <input type="hidden" name="{{ id }}[_action]" value="reset-assigned-meta">
        <p class="submit">
            <button type="submit" class="glsr-button button" name="reset-assigned-meta" id="reset-assigned-meta" data-ajax-click data-ajax-scroll>
                <span data-loading="<?= esc_attr_x('Resetting values, please wait...', 'admin-text', 'site-reviews'); ?>"><?= _x('Reset Meta Values', 'admin-text', 'site-reviews'); ?></span>
            </button>
        </p>
    </form>
</div>

<div class="glsr-card card">
    <h3><?= _x('Reset Permissions', 'admin-text', 'site-reviews'); ?></h3>
    <div class="components-notice is-info">
        <p class="components-notice__content"><?= _x('Hold down the ALT/Option key to perform a "Hard Reset"; this removes all Site Reviews capabilites from your Editor, Author, and Contributor roles before re-adding them.', 'admin-text', 'site-reviews'); ?></p>
    </div>
    <p><?= _x('Site Reviews provides custom post_type capabilities that mirror the capabilities of your posts by default. For example, if a user role has permission to edit others posts, then that role will also have permission to edit other users reviews.', 'admin-text', 'site-reviews'); ?></p>
    <p><?= _x('If you have changed the capabilities of your user roles (Administrator, Editor, Author, and Contributor) and you suspect that Site Reviews is not working correctly due to your changes, you may use this tool to reset the Site Reviews capabilities for your user roles.', 'admin-text', 'site-reviews'); ?></p>
    <form method="post">
        <?php wp_nonce_field('reset-permissions'); ?>
        <input type="hidden" name="{{ id }}[_action]" value="reset-permissions">
        <input type="hidden" name="{{ id }}[alt]" value="0" data-alt>
        <p class="submit">
            <button type="submit" class="glsr-button button" name="reset-permissions" id="reset-permissions" data-ajax-click data-ajax-scroll>
                <span data-alt-text="<?= esc_attr_x('Hard Reset Permissions', 'admin-text', 'site-reviews'); ?>" data-loading="<?= esc_attr_x('Resetting, please wait...', 'admin-text', 'site-reviews'); ?>"><?= _x('Reset Permissions', 'admin-text', 'site-reviews'); ?></span>
            </button>
        </p>
    </form>
</div>

<?php } ?>

<div class="glsr-card card">
    <h3><?= _x('Test IP Address Detection', 'admin-text', 'site-reviews'); ?></h3>
    <p><?= _x('When reviews are submitted on your website, Site Reviews detects the IP address of the person submitting the review and saves it to the submitted review. This allows you to limit review submissions or to blacklist reviewers based on their IP address. The IP address is also used by Akismet (if you have enabled the integration) to catch spam submissions.', 'admin-text', 'site-reviews'); ?></p>
    <p><?= _x('If you are getting an "unknown" value for IP addresses in your reviews, you may use this tool to check the visitor IP address detection.', 'admin-text', 'site-reviews'); ?></p>
    <form method="post">
        <?php wp_nonce_field('detect-ip-address'); ?>
        <input type="hidden" name="{{ id }}[_action]" value="detect-ip-address">
        <p class="submit">
            <button type="submit" class="glsr-button button" name="detect-ip-address" id="detect-ip-address" data-ajax-click data-ajax-scroll>
                <span data-loading="<?= esc_attr_x('Testing, please wait...', 'admin-text', 'site-reviews'); ?>"><?= _x('Test Detection', 'admin-text', 'site-reviews'); ?></span>
            </button>
        </p>
    </form>
</div>
