<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox is-fullwidth">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="welcome-v6_6_0">
            <span class="title">Version 6.6</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="welcome-v6_6_0" class="inside">
        <p><em>Release Date &mdash; March 10th, 2023</em></p>

        <h4>✨ New Features</h4>
        <ul>
            <li>Added integration with <a href="https://www.flycart.org/products/wordpress/wployalty" target="_blank">WooCommerce Loyalty Points and Rewards</a></li>
        </ul>

        <h4>🚨 Security</h4>
        <ul>
            <li>Fixed a <u>Broken Access Control vulnerability</u> which allowed any logged-in user to clear the Site Reviews > Tools > Console.</li>
            <li>Fixed a <u>Cross Site Scripting (XSS) vulnerability</u> which allowed a logged-in user (with the edit_posts capability) to insert malicious javascript code into a block attribute when adding it to a page.</li>
            <li>Fixed a <u>Cross Site Scripting (XSS) vulnerability</u> which allowed a logged-in user (with the edit_posts capability) to insert malicious javascript code into a shortcode attribute when adding it to a page.</li>
        </ul>

        <h4>🐞 Bugs Fixed</h4>
        <ul>
            <li>Fixed compatibility with <a href="https://wordpress.org/plugins/perfect-woocommerce-brands/" target="_blank">Perfect Brands for WooCommerce</a></li>
        </ul>
    </div>
</div>

