<?php defined('ABSPATH') || exit; ?>

<form method="post" class="glsr-form-sync glsr-status">
    <?php $selected = key($services); ?>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <td class="check-column glsr-radio-column"><span class="dashicons-before dashicons-update"></span></td>
                <th scope="col" class="column-primary"><?php echo _x('Service', 'admin-text', 'site-reviews'); ?></th>
                <th scope="col" class="column-total_fetched"><?php echo _x('Reviews', 'admin-text', 'site-reviews'); ?></th>
                <th scope="col" class="column-last_sync"><?php echo _x('Last Sync', 'admin-text', 'site-reviews'); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($services as $slug => $details) : ?>
            <tr class="service-<?php echo $slug; ?>">
                <th scope="row" class="check-column">
                    <input type="radio" name="{{ id }}[service]" value="<?php echo $slug; ?>" <?php checked($slug, $selected); ?>>
                </th>
                <td class="column-primary has-row-actions">
                    <strong><?php echo $details['name']; ?></strong>
                    <div class="row-actions">
                        <span><a href="<?php echo glsr_admin_url('settings', 'addons'); ?>"><?php echo _x('Settings', 'admin-text', 'site-reviews'); ?></a> | </span>
                        <span><a href="<?php echo glsr_admin_url('settings', 'licenses'); ?>"><?php echo _x('License', 'admin-text', 'site-reviews'); ?></a> | </span>
                        <span><a href="<?php echo glsr_admin_url('documentation', 'addons'); ?>"><?php echo _x('Documentation', 'admin-text', 'site-reviews'); ?></a></span>
                    </div>
                    <button type="button" class="toggle-row">
                        <span class="screen-reader-text"><?php echo _x('Show more details', 'admin-text', 'site-reviews'); ?></span>
                    </button>
                </td>
                <td class="column-total_fetched" data-colname="<?php echo esc_attr_x('Reviews', 'admin-text', 'site-reviews'); ?>">
                    <a href="<?php echo $details['reviews_url']; ?>"><?php echo $details['reviews_count']; ?></a>
                </td>
                <td class="column-last_sync" data-colname="<?php echo esc_attr_x('Last Sync', 'admin-text', 'site-reviews'); ?>">
                    <?php echo $details['last_sync']; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="no-items" style="display:table-cell!important;">
                    <div class="glsr-progress" data-active-text="<?php echo esc_attr_x('Please wait...', 'admin-text', 'site-reviews'); ?>">
                        <div class="glsr-progress-bar" style="width: 0%;">
                            <span class="glsr-progress-status"><?php echo _x('Inactive', 'admin-text', 'site-reviews'); ?></span>
                        </div>
                        <div class="glsr-progress-background">
                            <span class="glsr-progress-status"><?php echo _x('Inactive', 'admin-text', 'site-reviews'); ?></span>
                        </div>
                    </div>
                </td>
            </tr>
        </tfoot>
    </table>
    <div class="tablenav bottom">
        <button type="submit" class="glsr-button button"
            data-loading="<?php echo esc_attr_x('Syncing...', 'admin-text', 'site-reviews'); ?>"
        ><?php echo _x('Sync Reviews', 'admin-text', 'site-reviews'); ?>
        </button>
    </div>
</form>
