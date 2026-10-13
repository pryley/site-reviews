<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="faq-ipaddress-incorrect">
            <span class="title">Why is the IP address incorrectly detected?</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="faq-ipaddress-incorrect" class="inside">
        <p>If you are getting identical IP addresses for all your reviews (or if they are "unknown"), then your web server may be behind a CDN, firewall, or reverse proxy.</p>
        <p>If this is the case, you will need to provide Site Reviews with the HTTP request header that contains the real visitor IP address. If you are unsure what this means or how to find out which custom header is needed, please contact your hosting provider, server administrator, or the support team of your CDN or firewall service for assistance.</p>
        <p>Once you know the custom HTTP request header to use, you may add it with the following code snippet and then test it using the <?php echo glsr_admin_link('tools.general', 'Configure IP Address Detection', '#tools-ip-detection'); ?> tool.</p>
        <pre><code class="language-php">/**
 * Add a custom HTTP request header to fix IP address detection
 * @param \Geminilabs\Vectorface\Whip $whip
 * @return void
 */
add_action('site-reviews/whip', function ($whip) {
    $customHeader = 'HTTP_X_SUCURI_CLIENTIP'; // change the header as needed
    $whip->addCustomHeader($customHeader);
});</code></pre>
        <p>Site Reviews only uses the HTTP request header when a request comes from one of your trusted proxy servers. If you added a custom header with the code snippet above, use the following code snippet to add the IP addresses (or CIDR ranges) of your proxy servers.</p>
        <pre><code class="language-php">/**
 * Add the proxy servers that are trusted to send the HTTP request header
 * @param array $whitelist
 * @return array
 */
add_filter('site-reviews/whip/whitelist', function ($whitelist) {
    $method = \GeminiLabs\Vectorface\Whip\Whip::CUSTOM_HEADERS;
    $whitelist[$method]['ipv4'][] = '192.0.2.10'; // change the IP addresses as needed
    $whitelist[$method]['ipv4'][] = '198.51.100.0/24';
    $whitelist[$method]['ipv6'][] = '2001:db8::/32';
    return $whitelist;
});</code></pre>
    </div>
</div>
