<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox is-fullwidth">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="welcome-v8_2_0">
            <span class="title">Version 8.2</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="welcome-v8_2_0" class="inside">
        <p><em>Release Date &mdash; August 6th, 2026</em></p>

        <h4>✨ New Features</h4>
        <ul>
            <li>Added per-addon settings storage: each addon now keeps its settings in its own option</li>
            <li>Added support for the standalone "Site Review Premium" plugin (coming soon)</li>
            <li>Added support for WooCommerce "Customer review request" emails</li>
        </ul>

        <h4>💅🏼 Improved</h4>
        <ul>
            <li>Improved integration version checks to warn about untested versions instead of disabling the integration</li>
            <li>Improved the WooCommerce comments compatibility experiment to give plugins and themes more accurate results</li>
        </ul>

        <h4>📦 Updated</h4>
        <ul>
            <li>Updated <a href="https://actionscheduler.org/">Action Scheduler</a> to v4.1.0</li>
            <li>Updated the supported WooCommerce version to 11</li>
        </ul>

        <h4>🐞 Bugs Fixed</h4>
        <ul>
            <li>Fixed a "doing it wrong" notice raised by an integration's version notice</li>
            <li>Fixed a duplicate Reviews section in WooCommerce's redesigned Product Details block</li>
            <li>Fixed a fatal error on sites running MultilingualPress when it cannot report its version</li>
            <li>Fixed a PHP error when a review query is built with no arguments</li>
            <li>Fixed a PHP warning on sites running Loyalty Program for WooCommerce</li>
            <li>Fixed a PHP warning when importing WooCommerce product reviews</li>
            <li>Fixed an API request not being retried when the server asked for it to be</li>
            <li>Fixed an unrecognised admin request failing silently instead of being logged</li>
            <li>Fixed API request SSL certificate verification</li>
            <li>Fixed cached API responses never being cleared on sites using a persistent object cache</li>
            <li>Fixed deleted reviews being left in the review cache</li>
            <li>Fixed duplicating a review that had not accepted the terms</li>
            <li>Fixed imported reviews with no timestamp in the date being stamped with the time of the import</li>
            <li>Fixed imported WooCommerce product reviews losing their author</li>
            <li>Fixed PHP deprecation notices on the review revisions comparison screen</li>
            <li>Fixed plugin migrations rebuilding database indexes every time instead of only when needed</li>
            <li>Fixed Polylang translations never being applied to assigned pages, categories or users</li>
            <li>Fixed reviews being wrongly recorded as having accepted the terms on some sites</li>
            <li>Fixed stale settings keys not being cleaned after a plugin migration</li>
            <li>Fixed the "Approve" and "Unapproved" wording not being applied to the Publish metabox on the review editor</li>
            <li>Fixed the approve link capability check in the notification email</li>
            <li>Fixed the author link missing from reviews in the REST API</li>
            <li>Fixed the category priority cache not being cleared when a category priority is added or changed</li>
            <li>Fixed the classic widget title not being displayed</li>
            <li>Fixed the custom schema identifier being ignored unless the schema type was Custom</li>
            <li>Fixed the Documentation link on the plugins screen</li>
            <li>Fixed the notification and verification email settings offering invalid template tags</li>
            <li>Fixed the plugin rollback tool when JavaScript is unavailable</li>
            <li>Fixed the plugin's service container being unable to register a factory</li>
            <li>Fixed the plugins screen load time when the licence server is unreachable</li>
            <li>Fixed the post meta cache not being cleared when the rating counts are recalculated</li>
            <li>Fixed the Prosopo Procaptcha error details not being included in the log when a captcha is rejected</li>
            <li>Fixed the redirect after duplicating a review with the Duplicate Page plugin</li>
            <li>Fixed the review cache not being cleared when a rating is deleted</li>
            <li>Fixed the review submission limit matching too many previous reviews on sites using flexible assignment</li>
            <li>Fixed the Unapprove link publishing the review instead of unapproving it when javascript is disabled</li>
            <li>Fixed the update details check for inactive addons</li>
            <li>Fixed translatable strings that used multiple unnumbered placeholders — translations can now reorder them (existing translations of these strings will need to be updated)</li>
        </ul>

    </div>
</div>
