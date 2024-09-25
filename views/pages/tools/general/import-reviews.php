<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="tools-import-reviews">
            <span class="title dashicons-before dashicons-admin-tools"><?php echo esc_html_x('Import Reviews', 'admin-text', 'site-reviews'); ?></span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="tools-import-reviews" class="inside">
        <div class="glsr-notice-inline components-notice is-warning">
            <p class="components-notice__content">
                <?php echo sprintf(
                    esc_html_x('Please backup your database before running this tool! You can use the %s plugin to do this.', 'admin-text', 'site-reviews'),
                    '<a href="https://wordpress.org/plugins/updraftplus/" target="_blank">UpdraftPlus</a>'
                ); ?>
                <?php echo esc_html_x('Any entry in the CSV file that does not contain a required column value will be skipped.', 'admin-text', 'site-reviews'); ?>
            </p>
        </div>
        <div class="glsr-notice-inline components-notice is-info">
            <p class="components-notice__content">
                <?php echo sprintf(
                    esc_html_x('You can also use the WordPress %s and %s tools to export and import your reviews and categories.', 'admin-text', 'site-reviews'),
                    sprintf('<a href="%s">%s</a>', admin_url('export.php'), esc_html_x('Export', 'admin-text', 'site-reviews')),
                    sprintf('<a href="%s">%s</a>', admin_url('import.php'), esc_html_x('Import', 'admin-text', 'site-reviews'))
                ); ?>
            </p>
        </div>

        <h4><?php echo esc_html_x('Step 1: Download the CSV template file', 'admin-text', 'site-reviews'); ?></h4>
        <form method="post">
            <?php wp_nonce_field('download-csv-template'); ?>
            <input type="hidden" name="{{ id }}[_action]" value="download-csv-template">
            <button type="submit" class="glsr-button button button-large button-secondary">
                <?php echo esc_html_x('Download', 'admin-text', 'site-reviews'); ?>
            </button>
        </form>

        <h4><?php echo esc_html_x('Step 2: Enter reviews into the CSV file', 'admin-text', 'site-reviews'); ?></h4>
        <p><?php echo esc_html_x('Enter the reviews details into the template file and then save it. The CSV file should be encoded as UTF-8 and may contain the following columns:', 'admin-text', 'site-reviews'); ?></p>
        <div class="glsr-responsive-table">
            <table class="wp-list-table widefat striped">
                <thead>
                    <tr>
                        <th scope="col"><strong><?php echo esc_html_x('Column Name', 'admin-text', 'site-reviews'); ?></strong></th>
                        <th scope="col"><strong><?php echo esc_html_x('Required', 'admin-text', 'site-reviews'); ?></strong></th>
                        <th scope="col"><strong><?php echo esc_html_x('Description', 'admin-text', 'site-reviews'); ?></strong></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (glsr('Commands\DownloadCsvTemplate')->tableData() as $data) { ?>
                        <tr>
                            <td><strong><?php echo esc_html($data['name']); ?></strong></td>
                            <td><?php echo wp_kses_post($data['required']); ?></td>
                            <td><?php echo wp_kses_post($data['description']); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <h4><?php echo esc_html_x('Step 3: Upload the CSV file', 'admin-text', 'site-reviews'); ?></h4>
        <form method="post" class="wp-upload-form" enctype="multipart/form-data" onsubmit="submit.classList.add('is-busy'); submit.disabled = true;">
            <?php wp_nonce_field('import-reviews', '{{ id }}[_nonce]'); ?>
            <input type="hidden" name="{{ id }}[_action]" value="import-reviews">
            <p>
                <input type="file" name="import-files" accept="text/csv">
            </p>
            <div>
                <p>
                    <label for="csv_delimiter"><strong><?php echo esc_html_x('Delimiter', 'admin-text', 'site-reviews'); ?></strong></label><br>
                    <select name="{{ id }}[delimiter]" id="csv_delimiter">
                        <option value=""><?php echo _x('Detect automatically', 'admin-text', 'site-reviews'); ?></option>
                        <option value=","><?php echo _x('Comma (,)', 'admin-text', 'site-reviews'); ?></option>
                        <option value=";"><?php echo _x('Semicolon (;)', 'admin-text', 'site-reviews'); ?></option>
                    </select>
                </p>
                <p>
                    <label for="csv_date_format"><strong><?php echo esc_html_x('Date Format', 'admin-text', 'site-reviews'); ?></strong></label><br>
                    <select name="{{ id }}[date_format]" id="csv_date_format" required>
                        <option value=""><?php echo _x('Select the date format used in the reviews', 'admin-text', 'site-reviews'); ?></option>
                        <optgroup label="<?php echo sprintf('%s %s %s', _x('Year', 'admin-text', 'site-reviews'), _x('Month', 'admin-text', 'site-reviews'), _x('Day', 'admin-text', 'site-reviews')); ?>">
                            <option value="Y-m-d">2021-01-13</option>
                            <option value="Y-m-d H:i">2021-01-13 12:00</option>
                            <option value="Y-m-d H:i:s">2021-01-13 12:00:00 (used by the Export Reviews tool)</option>
                            <option value="Y/m/d">2021/01/13</option>
                            <option value="Y/m/d H:i">2021/01/13 12:00</option>
                            <option value="Y/m/d H:i:s">2021/01/13 12:00:00</option>
                        </optgroup>
                        <optgroup label="<?php echo sprintf('%s %s %s', _x('Month', 'admin-text', 'site-reviews'), _x('Day', 'admin-text', 'site-reviews'), _x('Year', 'admin-text', 'site-reviews')); ?>">
                            <option value="m-d-Y">01-13-2021</option>
                            <option value="m-d-Y H:i">01-13-2021 12:00</option>
                            <option value="m-d-Y H:i:s">01-13-2021 12:00:00</option>
                            <option value="m/d/Y">01/13/2021</option>
                            <option value="m/d/Y H:i">01/13/2021 12:00</option>
                            <option value="m/d/Y H:i:s">01/13/2021 12:00:00</option>
                        </optgroup>
                        <optgroup label="<?php echo sprintf('%s %s %s', _x('Day', 'admin-text', 'site-reviews'), _x('Month', 'admin-text', 'site-reviews'), _x('Year', 'admin-text', 'site-reviews')); ?>">
                            <option value="d-m-Y">13-01-2021</option>
                            <option value="d-m-Y H:i">13-01-2021 12:00</option>
                            <option value="d-m-Y H:i:s">13-01-2021 12:00:00</option>
                            <option value="d/m/Y">13/01/2021</option>
                            <option value="d/m/Y H:i">13/01/2021 12:00</option>
                            <option value="d/m/Y H:i:s">13/01/2021 12:00:00</option>
                        </optgroup>
                    </select>
                </p>
            </div>
            <div>
                <button type="submit" class="glsr-button button button-large button-primary"
                    data-ajax-import
                    data-loading="<?php echo esc_attr_x('Importing reviews, please wait...', 'admin-text', 'site-reviews'); ?>"
                ><?php echo _x('Import Reviews', 'admin-text', 'site-reviews'); ?>
                </button>
            </div>
        </form>
    </div>
</div>
