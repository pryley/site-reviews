<?php defined('ABSPATH') || exit; ?>

<?php if (glsr()->hasPermission('settings')): ?>
<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="tools-geolocate-reviews">
            <span class="title dashicons-before dashicons-admin-tools"><?php echo _x('Geolocate Reviews', 'admin-text', 'site-reviews'); ?></span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="tools-geolocate-reviews" class="inside">
        <?php /* translators: %s: link to the IP-API website */ echo wp_get_admin_notice(
            sprintf(
                _x('This tool uses the free %s Geolocation API service to extract location data from IP Addresses.', 'admin-text', 'site-reviews'),
                '<a href="https://ip-api.com/">IP-API</a>'
            ),
            ['type' => 'info', 'additional_classes' => ['inline']]
        ); ?>
        <p><?php echo _x('Site Reviews stores the IP address of the reviewer when they submit a review. The IP address is used during review validation and to prevent abuse.', 'admin-text', 'site-reviews'); ?></p>
        <p><?php echo _x('This tool will extract missing geolocation (country, region/state, and city) of reviews that have a valid IP addresses. This location data can be used to display the location in the review (e.g. the flag of the reviewer\'s country).', 'admin-text', 'site-reviews'); ?></p>
        <p><?php echo _x('If you want to remove the geolocation data from all of your reviews, click the <strong>Remove</strong> button.', 'admin-text', 'site-reviews'); ?></p>
        <form method="post" enctype="multipart/form-data" onsubmit="submit.disabled = true;">
            <?php wp_nonce_field('geolocate-reviews', '{{ id }}[_nonce]'); ?>
            <input type="hidden" name="{{ id }}[_action]" value="geolocate-reviews">
            <input type="hidden" name="{{ id }}[alt]" value="0" data-alt>
            <button type="submit" class="glsr-button button button-large button-primary"
                data-ajax-click
                data-ajax-scroll
                data-loading="<?php echo esc_attr_x('Queueing geolocation, please wait...', 'admin-text', 'site-reviews'); ?>"
            ><?php echo _x('Geolocate Reviews', 'admin-text', 'site-reviews'); ?>
            </button>
            <button type="submit" class="glsr-button button button-large button-secondary"
                data-ajax-click
                data-ajax-scroll
                data-alt
                data-loading="<?php echo esc_attr_x('Removing all geolocated data, please wait...', 'admin-text', 'site-reviews'); ?>"
            ><?php echo _x('Remove', 'admin-text', 'site-reviews'); ?>
            </button>
        </form>
    </div>
</div>
<?php endif; ?>
