<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="faq-ipaddress-incorrect">
            <span class="title">Why is the IP address incorrectly detected?</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="faq-ipaddress-incorrect" class="inside">
        <p>If you are getting the same IP address for all your reviews (or if they are "unknown"), your web server may be behind a CDN, firewall, or reverse proxy.</p>
        <p>To fix this, use the <?php echo glsr_admin_link('tools.general', 'Configure IP Address Detection', '#tools-ip-detection'); ?> tool to provide Site Reviews with the HTTP request header that contains the real visitor IP address.</p>
    </div>
</div>
