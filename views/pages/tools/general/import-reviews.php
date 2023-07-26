<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="tools-import-reviews">
            <span class="title dashicons-before dashicons-admin-tools"><?= _x('Import Reviews', 'admin-text', 'site-reviews'); ?></span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="tools-import-reviews" class="inside">
        <div class="glsr-notice-inline components-notice is-warning">
            <p class="components-notice__content">
                <?= sprintf(
                    _x('Please backup your database before running this tool! You can use the %s plugin to do this.', 'admin-text', 'site-reviews'),
                    '<a href="https://wordpress.org/plugins/updraftplus/" target="_blank">UpdraftPlus</a>'
                ); ?>
                <?= _x('Any entry in the CSV file that does not contain a required column value will be skipped.', 'admin-text', 'site-reviews'); ?>
            </p>
        </div>
        <div class="glsr-notice-inline components-notice is-info">
            <p class="components-notice__content">
                <?= sprintf(
                    _x('You can also use the WordPress %s and %s tools to export and import your reviews and categories.', 'admin-text', 'site-reviews'),
                    sprintf('<a href="%s">%s</a>', admin_url('export.php'), _x('Export', 'admin-text', 'site-reviews')),
                    sprintf('<a href="%s">%s</a>', admin_url('import.php'), _x('Import', 'admin-text', 'site-reviews'))
                ); ?>
            </p>
        </div>
        <p><?= sprintf(
            _x('Here you can import third party reviews from a %s file. The CSV file should be encoded as UTF-8, include a header row, and may contain the following columns:', 'admin-text', 'site-reviews'),
            '<code>*.csv</code>'
        ); ?></p>
        <div class="glsr-responsive-table">
            <table class="wp-list-table widefat striped">
                <thead>
                    <tr>
                        <th scope="col"><strong>Column Name</strong></th>
                        <th scope="col"><strong>Required</strong></th>
                        <th scope="col"><strong>Description</strong></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>assigned_posts</strong></td>
                        <td><span class="glsr-tag"><?= _x('No', 'admin-text', 'site-reviews'); ?></span></td>
                        <td><?= _x('The Posts that the review is assigned to (separate multiple IDs with a comma)', 'admin-text', 'site-reviews'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>assigned_terms</strong></td>
                        <td><span class="glsr-tag"><?= _x('No', 'admin-text', 'site-reviews'); ?></span></td>
                        <td><?= _x('The Categories that the review is assigned to (separate multiple IDs with a comma)', 'admin-text', 'site-reviews'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>assigned_users</strong></td>
                        <td><span class="glsr-tag"><?= _x('No', 'admin-text', 'site-reviews'); ?></span></td>
                        <td><?= _x('The Users that the review is assigned to (separate multiple IDs with a comma)', 'admin-text', 'site-reviews'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>author_id</strong></td>
                        <td><span class="glsr-tag"><?= _x('No', 'admin-text', 'site-reviews'); ?></span></td>
                        <td><?= _x('The User ID of the reviewer', 'admin-text', 'site-reviews'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>avatar</strong></td>
                        <td><span class="glsr-tag"><?= _x('No', 'admin-text', 'site-reviews'); ?></span></td>
                        <td><?= _x('The avatar URL of the reviewer', 'admin-text', 'site-reviews'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>content</strong></td>
                        <td><span class="glsr-tag"><?= _x('No', 'admin-text', 'site-reviews'); ?></span></td>
                        <td><?= _x('The review', 'admin-text', 'site-reviews'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>date</strong></td>
                        <td><span class="glsr-tag glsr-tag-required"><?= _x('Yes', 'admin-text', 'site-reviews'); ?></span></td>
                        <td><?= _x('The review date', 'admin-text', 'site-reviews'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>date_gmt</strong></td>
                        <td><span class="glsr-tag"><?= _x('No', 'admin-text', 'site-reviews'); ?></span></td>
                        <td><?= _x('The review GMT date', 'admin-text', 'site-reviews'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>email</strong></td>
                        <td><span class="glsr-tag"><?= _x('No', 'admin-text', 'site-reviews'); ?></span></td>
                        <td><?= _x('The reviewer\'s email', 'admin-text', 'site-reviews'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>ip_address</strong></td>
                        <td><span class="glsr-tag"><?= _x('No', 'admin-text', 'site-reviews'); ?></span></td>
                        <td><?= _x('The IP address of the reviewer', 'admin-text', 'site-reviews'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>is_approved</strong></td>
                        <td><span class="glsr-tag"><?= _x('No', 'admin-text', 'site-reviews'); ?></span></td>
                        <td><?= _x('TRUE or FALSE', 'admin-text', 'site-reviews'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>is_pinned</strong></td>
                        <td><span class="glsr-tag"><?= _x('No', 'admin-text', 'site-reviews'); ?></span></td>
                        <td><?= _x('TRUE or FALSE', 'admin-text', 'site-reviews'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>is_verified</strong></td>
                        <td><span class="glsr-tag"><?= _x('No', 'admin-text', 'site-reviews'); ?></span></td>
                        <td><?= _x('TRUE or FALSE', 'admin-text', 'site-reviews'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>name</strong></td>
                        <td><span class="glsr-tag"><?= _x('No', 'admin-text', 'site-reviews'); ?></span></td>
                        <td><?= _x('The reviewer\'s name', 'admin-text', 'site-reviews'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>rating</strong></td>
                        <td><span class="glsr-tag glsr-tag-required"><?= _x('Yes', 'admin-text', 'site-reviews'); ?></span></td>
                        <td><?= sprintf(_x('A number from 0-%d', 'admin-text', 'site-reviews'), glsr()->constant('MAX_RATING', 'GeminiLabs\SiteReviews\Modules\Rating')); ?></td>
                    </tr>
                    <tr>
                        <td><strong>response</strong></td>
                        <td><span class="glsr-tag"><?= _x('No', 'admin-text', 'site-reviews'); ?></span></td>
                        <td><?= _x('The review response', 'admin-text', 'site-reviews'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>score</strong></td>
                        <td><span class="glsr-tag"><?= _x('No', 'admin-text', 'site-reviews'); ?></span></td>
                        <td><?= _x('A positive or negative whole number', 'admin-text', 'site-reviews'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>terms</strong></td>
                        <td><span class="glsr-tag"><?= _x('No', 'admin-text', 'site-reviews'); ?></span></td>
                        <td><?= _x('TRUE or FALSE', 'admin-text', 'site-reviews'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>title</strong></td>
                        <td><span class="glsr-tag"><?= _x('No', 'admin-text', 'site-reviews'); ?></span></td>
                        <td><?= _x('The title of the review', 'admin-text', 'site-reviews'); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <form method="post" enctype="multipart/form-data" onsubmit="submit.classList.add('is-busy'); submit.disabled = true;">
            <?php wp_nonce_field('import-reviews'); ?>
            <input type="hidden" name="{{ id }}[_action]" value="import-reviews">
            <p>
                <input type="file" name="import-file" accept="text/csv">
            </p>
            <p>
                <label for="csv_delimiter"><strong><?= _x('Delimiter', 'admin-text', 'site-reviews'); ?></strong></label><br>
                <select name="{{ id }}[delimiter]" id="csv_delimiter">
                    <option value=""><?= _x('Detect automatically', 'admin-text', 'site-reviews'); ?></option>
                    <option value=","><?= _x('Comma (,)', 'admin-text', 'site-reviews'); ?></option>
                    <option value=";"><?= _x('Semicolon (;)', 'admin-text', 'site-reviews'); ?></option>
                </select>
            </p>
            <p>
                <label for="csv_date_format"><strong><?= _x('Date Format', 'admin-text', 'site-reviews'); ?></strong></label><br>
                <select name="{{ id }}[date_format]" id="csv_date_format" required>
                    <option value=""><?= _x('Select the date format used in the reviews', 'admin-text', 'site-reviews'); ?></option>
                    <optgroup label="<?= sprintf('%s %s %s', _x('Year', 'admin-text', 'site-reviews'), _x('Month', 'admin-text', 'site-reviews'), _x('Day', 'admin-text', 'site-reviews')); ?>">
                        <option value="Y-m-d">2021-01-13</option>
                        <option value="Y-m-d H:i">2021-01-13 12:00</option>
                        <option value="Y-m-d H:i:s">2021-01-13 12:00:00</option>
                        <option value="Y/m/d">2021/01/13</option>
                        <option value="Y/m/d H:i">2021/01/13 12:00</option>
                        <option value="Y/m/d H:i:s">2021/01/13 12:00:00</option>
                    </optgroup>
                    <optgroup label="<?= sprintf('%s %s %s', _x('Month', 'admin-text', 'site-reviews'), _x('Day', 'admin-text', 'site-reviews'), _x('Year', 'admin-text', 'site-reviews')); ?>">
                        <option value="m-d-Y">01-13-2021</option>
                        <option value="m-d-Y H:i">01-13-2021 12:00</option>
                        <option value="m-d-Y H:i:s">01-13-2021 12:00:00</option>
                        <option value="m/d/Y">01/13/2021</option>
                        <option value="m/d/Y H:i">01/13/2021 12:00</option>
                        <option value="m/d/Y H:i:s">01/13/2021 12:00:00</option>
                    </optgroup>
                    <optgroup label="<?= sprintf('%s %s %s', _x('Day', 'admin-text', 'site-reviews'), _x('Month', 'admin-text', 'site-reviews'), _x('Year', 'admin-text', 'site-reviews')); ?>">
                        <option value="d-m-Y">13-01-2021</option>
                        <option value="d-m-Y H:i">13-01-2021 12:00</option>
                        <option value="d-m-Y H:i:s">13-01-2021 12:00:00</option>
                        <option value="d/m/Y">13/01/2021</option>
                        <option value="d/m/Y H:i">13/01/2021 12:00</option>
                        <option value="d/m/Y H:i:s">13/01/2021 12:00:00</option>
                    </optgroup>
                </select>
            </p>
            </button>
            <button type="submit" class="glsr-button components-button is-primary"
                data-expand="#tools-import-reviews"
                data-loading="<?= esc_attr_x('Importing reviews, please wait...', 'admin-text', 'site-reviews'); ?>"
            ><?= _x('Import Reviews', 'admin-text', 'site-reviews'); ?>
            </button>
        </form>
    </div>
</div>
