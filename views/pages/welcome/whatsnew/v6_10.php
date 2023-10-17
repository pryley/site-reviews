<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox is-fullwidth">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="welcome-v6_10_0">
            <span class="title">Version 6.10</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="welcome-v6_10_0" class="inside">
        <p><em>Release Date &mdash; August 5th, 2023</em></p>

        <h4>✨ New Features</h4>
        <ul>
            <li>Added integration with <a target="_blank" href="https://mycred.me/">myCRED</a></li>
            <li>Added listtable filter for Accepted Terms (All Reviews admin page)</li>
            <li>Added options to override the Site Reviews shortcode settings for a WooCommerce product (find the options in the "product data" metabox)</li>
            <li>Added priority option for categories</li>
        </ul>

        <h4>🐞 Bugs Fixed</h4>
        <ul>
            <li>Fixed Bulk Edit from removing author assignments</li>
            <li>Fixed compatibility with WordPress v5.8 and v5.9</li>
            <li>Fixed excerpt fallback when PHP Intl extension is disabled</li>
            <li>Fixed honeypot field IDs</li>
            <li>Fixed Migrate Plugin tool (force-running plugin migration will fix orphaned foreign constraints related to old database table prefixes)</li>
            <li>Fixed "read more" links in exceprts</li>
            <li>Fixed registration of assignment meta keys</li>
            <li>Fixed <code>{review_assigned_links}</code> template tag in email notifications</li>
            <li>Fixed Ultimate Member avatar compatibility</li>
            <li>Fixed unicode support for author name in reviews</li>
            <li>Fixed WPEngine detection in System Info</li>
        </ul>

        <h4>📢 Changed</h4>
        <ul>
            <li>Changed the console log level for validation errors from Warning to Info</li>
        </ul>

        <h4>📦 Updated</h4>
        <ul>
            <li>Updated API documentation</li>
            <li>Updated documentation</li>
        </ul>
    </div>
</div>

