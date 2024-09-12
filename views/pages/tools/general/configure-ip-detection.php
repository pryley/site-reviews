<?php defined('ABSPATH') || exit;

$setting = glsr()->args(get_option(glsr()->prefix.'ip_proxy'));
$proxyHeader = $setting->sanitize('proxy_http_header', 'id');
$trustedProxies = $setting->sanitize('trusted_proxies', 'text-multiline');
$trustedProxies = explode("\n", $trustedProxies);
$trustedProxies = array_filter($trustedProxies, function ($range) {
    [$ip] = explode('/', $range);
    return !empty(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6));
});
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
        <div class="glsr-notice-inline components-notice is-warning">
            <p class="components-notice__content">
                <?php echo _x('Be careful when enabling a proxy HTTP header if you do not have a front-end proxy configuration because it may allow visitors to spoof their IP address.', 'admin-text', 'site-reviews'); ?>
            </p>
        </div>
        <div class="glsr-notice-inline components-notice is-info">
            <p class="components-notice__content">
                <?php echo _x('If you are unsure which proxy HTTP header to use, contact the technical support staff of your hosting provider and read their documentation to determine the correct configuration for your website.', 'admin-text', 'site-reviews'); ?>
            </p>
        </div>
        <p><?php echo _x('When a review is submitted on your website, Site Reviews detects the IP address of the person submitting it and saves it to the review. Site Reviews uses the IP address in the review limits and blacklist settings, and the Akismet and Captcha integrations to catch spam submissions.', 'admin-text', 'site-reviews'); ?></p>
        <p><?php echo _x('If you get incorrect IP address values in your reviews, there might be a reverse proxy, load balancer, cache, Cloudflare, CDN, or any other type of proxy in front of your web server that "proxies" traffic to your website. If so, select the proxy HTTP header that contains the real visitor IP address.', 'admin-text', 'site-reviews'); ?></p>
        <form method="post">
            <?php wp_nonce_field('ip-address-detection'); ?>
            <input type="hidden" name="{{ id }}[_action]" value="ip-address-detection">
            <input type="hidden" name="{{ id }}[alt]" value="0" data-alt>
            <div>
                <p>
                    <label for="proxy_http_header">
                        <strong><?php echo _x('Proxy HTTP Header', 'admin-text', 'site-reviews'); ?></strong>
                    </label>
                    <br />
                    <select id="proxy_http_header" name="{{ id }}[proxy_http_header]">
                        <option value=""<?php selected('', $proxyHeader); ?>'>None</option>
                        <option value="cf-connecting-ip"<?php selected('cf-connecting-ip', $proxyHeader); ?>'>CF-Connecting-IP</option>
                        <option value="client-ip"<?php selected('client-ip', $proxyHeader); ?>'>Client-IP</option>
                        <option value="forwarded"<?php selected('forwarded', $proxyHeader); ?>'>Forwarded</option>
                        <option value="forwarded-for"<?php selected('forwarded-for', $proxyHeader); ?>'>Forwarded-For</option>
                        <option value="incap-client-ip"<?php selected('incap-client-ip', $proxyHeader); ?>'>Incap-Client-IP</option>
                        <option value="x-cluster-client-ip"<?php selected('x-cluster-client-ip', $proxyHeader); ?>'>X-Cluster-Client-IP</option>
                        <option value="x-forwarded"<?php selected('x-forwarded', $proxyHeader); ?>'>X-Forwarded</option>
                        <option value="x-forwarded-for"<?php selected('x-forwarded-for', $proxyHeader); ?>'>X-Forwarded-For</option>
                        <option value="x-real-ip"<?php selected('x-real-ip', $proxyHeader); ?>'>X-Real-IP</option>
                    </select>
                </p>
                <p class="<?php echo empty($proxyHeader) ? 'hidden' : ''; ?>">
                    <label for="trusted_proxies">
                        <strong><?php echo _x('Trusted Proxies', 'admin-text', 'site-reviews'); ?></strong>
                    </label>
                    <br />
                    <textarea id="trusted_proxies" name="{{ id }}[trusted_proxies]" class="large-text code" rows="5"><?php echo $trustedProxies; ?></textarea>
                    <br />
                    <span class="description"><?php echo _x('These IPs (or CIDR ranges) will be ignored when determining the requesting IP via the selected proxy HTTP header. Enter one IP or CIDR range per line.', 'admin-text', 'site-reviews'); ?></span>
                </p>
            </div>
            <div>
                <p>
                    <button type="submit" class="glsr-button button button-large button-primary"
                        data-ajax-click
                        data-ajax-scroll
                        data-loading="<?php echo esc_attr_x('Saving, please wait...', 'admin-text', 'site-reviews'); ?>"
                    ><?php echo _x('Save', 'admin-text', 'site-reviews'); ?>
                    </button>
                    <button type="submit" class="glsr-button button button-large button-secondary"
                        data-ajax-click
                        data-ajax-scroll
                        data-alt
                        data-loading="<?php echo esc_attr_x('Testing, please wait...', 'admin-text', 'site-reviews'); ?>"
                    ><?php echo _x('Test IP Address Detection', 'admin-text', 'site-reviews'); ?>
                    </button>
                </p>
            </div>
        </form>
    </div>
</div>
