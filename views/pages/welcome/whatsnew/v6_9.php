<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox is-fullwidth">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="welcome-v6_9_0">
            <span class="title">Version 6.9</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="welcome-v6_9_0" class="inside">
        <p><em>Release Date &mdash; May 21st, 2023</em></p>

        <h4>✨ New Features</h4>
        <ul>
            <li>Added Assigned Links to Discord notifications</li>
            <li>Added support for field descriptions (<a href="https://niftyplugins.com/plugins/site-reviews-forms/" target="_blank">Review Forms</a> addon required)</li>
            <li>Updated the Date sanitizer to allow a date format</li>
            <li>Updated the Slack integration to use block composition</li>
        </ul>

        <h4>🐞 Bugs Fixed</h4>
        <ul>
            <li>Fixed a PHP error caused by a bug in several third-party migration plugins</li>
            <li>Fixed excerpt character split</li>
            <li>Fixed Discord notification avatar and username (Site Reviews now uses the values configured in the Discord Webhook)</li>
            <li>Fixed paragraph spacing in reviews</li>
            <li>Fixed review caching</li>
            <li>Fixed settings sanitization</li>
            <li>Fixed styled SELECT elements</li>
        </ul>

        <h4>📢 Changed</h4>
        <ul>
            <li>Deprecated the "site-reviews/slack/compose" hook (use the "site-reviews/slack/notification" hook instead)</li>
        </ul>

        <h4>📦 Updated</h4>
        <ul>
            <li>Updated <a href="https://github.com/woocommerce/action-scheduler" target="_blank">Action Scheduler</a> to v3.6.0</li>
        </ul>
    </div>
</div>

