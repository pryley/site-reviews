<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox is-fullwidth">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="welcome-v8_1_0">
            <span class="title">Version 8.1</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="welcome-v8_1_0" class="inside">
        <p><em>Release Date &mdash; July 11th, 2026</em></p>

        <h4>✨ New Features</h4>
        <ul>
            <li>Added a migration to backfill missing <code>post_date_gmt</code> values on existing reviews</li>
        </ul>

        <h4>💅🏼 Improved</h4>
        <ul>
            <li>Improved block editor performance by prefetching component options</li>
            <li>Improved encryption key derivation to use HKDF (existing encrypted data still decrypts)</li>
            <li>Improved escaping of admin notices on the Scheduled Actions page</li>
            <li>Improved the markup of admin error notices</li>
        </ul>

        <h4>📦 Updated</h4>
        <ul>
            <li>Updated <a href="https://actionscheduler.org/">Action Scheduler</a> to v4.0.0 (failed actions are now purged after 3 months)</li>
            <li>Updated the minimum required WordPress version to 6.8</li>
        </ul>

        <h4>🐞 Bugs Fixed</h4>
        <ul>
            <li>Fixed admin settings page styling in WordPress 7.0</li>
            <li>Fixed an unbounded reviews-per-page query parameter that could exhaust server resources</li>
            <li>Fixed color sanitization to ignore empty values</li>
            <li>Fixed Discord and Slack webhook URL validation to prevent a host-matching bypass</li>
            <li>Fixed geolocation batch processing to reschedule failed or rate-limited requests instead of blocking the queue worker</li>
            <li>Fixed geolocation retries being silently dropped by the scheduled actions queue</li>
            <li>Fixed malformed markup in the reviews list-table rating column for ratings greater than 5</li>
            <li>Fixed random review ordering so paginated results stay consistent within the hour</li>
        </ul>

    </div>
</div>
