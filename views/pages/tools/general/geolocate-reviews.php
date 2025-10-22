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
        <div class="glsr-notice-inline components-notice is-info">
            <p class="components-notice__content">
                <?php printf(
                    _x('This tool uses the free %s Geolocation API service to extract location data from IP Addresses.', 'admin-text', 'site-reviews'),
                    '<a href="https://ip-api.com/">IP-API</a>'
                ); ?>
            </p>
        </div>
        <p><?php echo _x('Site Reviews stores the IP address of the reviewer when they submit a review. The IP address is used during review validation and to prevent abuse.', 'admin-text', 'site-reviews'); ?></p>
        <p><?php echo _x('This tool will extract missing geolocation (country, region/state, and city) of reviews that have a valid IP addresses. This location data can be used to display the location in the review (e.g. the flag of the reviewer\'s country).', 'admin-text', 'site-reviews'); ?></p>
        <form method="post" enctype="multipart/form-data" onsubmit="submit.disabled = true;">
            <?php wp_nonce_field('geolocate-reviews', '{{ id }}[_nonce]'); ?>
            <input type="hidden" name="{{ id }}[_action]" value="geolocate-reviews">
            <button type="submit" class="glsr-button button button-large button-primary"
                data-ajax-click
                data-ajax-scroll
                data-loading="<?php echo esc_attr_x('Queueing geolocation, please wait...', 'admin-text', 'site-reviews'); ?>"
            ><?php echo _x('Geolocate Reviews', 'admin-text', 'site-reviews'); ?>
            </button>
        </form>
    </div>
</div>
<?php endif; ?>
