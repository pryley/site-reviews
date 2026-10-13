<?php defined('ABSPATH') || exit;

$clientIp = glsr(\GeminiLabs\SiteReviews\Modules\ClientIp::class);
$setting = glsr()->args(get_option(glsr()->prefix.'ip_proxy'));
$proxyHeader = $clientIp->proxyHeader();
$proxyHeaders = $clientIp->headers();
// A saved header that is not in the list was entered as a custom header.
$isCustomHeader = !empty($proxyHeader) && !array_key_exists($proxyHeader, $proxyHeaders);
$trustedProxies = $setting->sanitize('trusted_proxies', 'text-multiline');
$trustedProxies = $clientIp->ranges($trustedProxies);
$trustedProxies = implode("\n", $trustedProxies);

?>

<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="tools-ip-detection">
            <span class="title dashicons-before dashicons-admin-tools"><?php echo _x('Configure IP Address Detection', 'admin-text', 'site-reviews'); ?></span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="tools-ip-detection" class="inside">
        <?php echo wp_get_admin_notice(
            _x('A proxy HTTP header will only be used when a request comes from one of your trusted proxies.', 'admin-text', 'site-reviews'),
            ['type' => 'warning', 'additional_classes' => ['inline']]
        ); ?>
        <?php echo wp_get_admin_notice(
            _x('If you are unsure which proxy HTTP header to use, contact the technical support staff of your hosting provider or read their documentation to determine the correct configuration for your website.', 'admin-text', 'site-reviews'),
            ['type' => 'info', 'additional_classes' => ['inline']]
        ); ?>
        <p><?php echo _x('When a review is submitted on your website, Site Reviews detects the IP address of the person submitting it and saves it to the review. Site Reviews uses the IP address for the Review Limits and Blacklist features; it is also used by the Akismet and Captcha integrations to handle SPAM reviews.', 'admin-text', 'site-reviews'); ?></p>
        <p><?php echo _x('If your reviews are being submitted with an incorrect IP address, your web server likely has a reverse proxy, load balancer, cache, Cloudflare, CDN, or some other type of proxy service that handles traffic to your website.', 'admin-text', 'site-reviews'); ?></p>
        <p><?php echo _x('Here you can select the Proxy HTTP header that contains the real visitor IP address and the list of trusted IP addresses used by the proxy service.', 'admin-text', 'site-reviews'); ?></p>
        <form method="post">
            <?php wp_nonce_field('ip-address-detection'); ?>
            <input type="hidden" name="{{ id }}[_action]" value="ip-address-detection">
            <input type="hidden" name="{{ id }}[alt]" value="0" data-alt>
            <fieldset>
                <div>
                    <label for="proxy_http_header">
                        <?php echo _x('Proxy HTTP Header', 'admin-text', 'site-reviews'); ?>
                    </label>
                    <select id="proxy_http_header" name="{{ id }}[proxy_http_header]">
                        <option value=""<?php selected('', $proxyHeader); ?>>None</option>
                        <?php foreach ($proxyHeaders as $header => $label) { ?>
                            <option value="<?php echo esc_attr($header); ?>"<?php selected($header, $proxyHeader); ?>><?php echo esc_html($label); ?></option>
                        <?php } ?>
                        <option value="custom"<?php selected($isCustomHeader); ?>><?php echo _x('Custom', 'admin-text', 'site-reviews'); ?></option>
                    </select>
                </div>
                <div class="<?php echo $isCustomHeader ? '' : 'hidden'; ?>">
                    <label for="custom_http_header">
                        <?php echo _x('Custom HTTP Header', 'admin-text', 'site-reviews'); ?>
                    </label>
                    <input type="text" id="custom_http_header" name="{{ id }}[custom_http_header]" value="<?php echo esc_attr($isCustomHeader ? $proxyHeader : ''); ?>" placeholder="X-Client-IP">
                </div>
                <div class="full-width<?php echo empty($proxyHeader) ? ' hidden' : ''; ?>">
                    <label for="trusted_proxies">
                        <?php echo _x('Trusted Proxies', 'admin-text', 'site-reviews'); ?>
                    </label>
                    <textarea id="trusted_proxies" name="{{ id }}[trusted_proxies]" class="code" rows="5"><?php echo $trustedProxies; ?></textarea>
                    <span class="description"><?php echo _x('Enter the IP addresses (or CIDR ranges) of your proxy servers, one per line. The proxy HTTP header is only used when a request comes from one of these addresses.', 'admin-text', 'site-reviews'); ?></span>
                </div>
            </fieldset>
            <div>
                <button type="submit" class="glsr-button button button-large button-primary"
                    data-ajax-click
                    data-ajax-scroll
                    data-loading="<?php echo esc_attr_x('Saving, please wait...', 'admin-text', 'site-reviews'); ?>"
                ><?php echo _x('Save', 'admin-text', 'site-reviews'); ?>
                </button>
                <button type="submit" class="glsr-button button button-large"
                    data-ajax-click
                    data-ajax-scroll
                    data-alt
                    data-loading="<?php echo esc_attr_x('Testing, please wait...', 'admin-text', 'site-reviews'); ?>"
                ><?php echo _x('Test IP Address Detection', 'admin-text', 'site-reviews'); ?>
                </button>
            </div>
        </form>
    </div>
</div>
