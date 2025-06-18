<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox is-fullwidth">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="welcome-v7_2_0">
            <span class="title">Version 7.2</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="welcome-v7_2_0" class="inside">
        <p><em>Release Date &mdash; October 24th, 2024</em></p>

        <h4>✨ New Features</h4>
        <ul>
            <li>Added global color support to the Elementor widgets.</li>
            <li>Added <a href="https://prosopo.io/" target="_blank">Prosopo Procaptcha</a> integration</li>
            <li>Added <a href="https://ultimatemember.com/" target="_blank">Ultimate Member</a> integration</li>
            <li>Added support for Range fields (<?php echo glsr_premium_link('site-reviews-forms'); ?> addon required)</li>
            <li>Added support for responsive custom fields (<?php echo glsr_premium_link('site-reviews-forms'); ?> addon required)</li>
        </ul>

        <h4>🐞 Bugs Fixed</h4>
        <ul>
            <li>Fixed compatibility with WooCommerce block themes</li>
            <li>Fixed license key masking when an addon is deactivated</li>
            <li>Fixed setting field responsive CSS</li>
            <li>Fixed WooCommerce product rating counts when using the Import Product Reviews tool to revert a product ratings migration.</li>
        </ul>
    </div>
</div>
