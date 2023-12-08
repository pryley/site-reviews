<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="faq-dont-store-ipaddress">
            <span class="title">How do I prevent the IP address from being saved with the review?</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="faq-dont-store-ipaddress" class="inside">
        <div class="glsr-notice-inline components-notice is-info">
            <p class="components-notice__content">
                <a href="https://en.wikipedia.org/wiki/General_Data_Protection_Regulation">GDPR</a> EU law does not require you to obtain consent to log an IP address if the purpose is of legitimate interest. Logging IP addresses for the purpose of security is an extremely widespread practice and is a legitimate interest to comply with standard security practices. See also: <a href="https://law.stackexchange.com/a/28609">https://law.stackexchange.com/a/28609</a>.
            </p>
        </div>
        <p>Site Reviews saves the IP address to the review when it is submitted for security purposes only (i.e. to prevent spam and automated reviews). If you do not want the IP address logged then you can use the following code snippet:</p>
        <pre><code class="language-php">/**
 * Prevents the IP address from being saved with the review
 * Paste this code in your theme's functions.php file.
 * @param array $values
 * @return array
 */
add_filter('site-reviews/create/review-values', function ($values) {
    $values['ip_address'] = '';
    return $values;
});</code></pre>
    </div>
</div>
