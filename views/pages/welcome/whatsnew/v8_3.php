<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox is-fullwidth open">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="true" aria-controls="welcome-v8_3_0">
            <span class="title">Version 8.3</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="welcome-v8_3_0" class="inside">
        <p><em>Release Date &mdash; September 2nd, 2026</em></p>

        <h4>✨ New Features</h4>
        <ul>
            <li>Added REST API endpoints for review submission and pagination; the frontend now uses them with an automatic fallback to admin-ajax</li>
        </ul>

        <h4>💅🏼 Improved</h4>
        <ul>
            <li>Improved compatibility with firewalls that block wp-admin requests</li>
            <li>Improved the encryption key derivation; on sites without unique security keys in wp-config.php, links in emails sent before this update must be re-sent</li>
            <li>Improved the failed auto-update email to include a reason when an addon license does not allow the update</li>
            <li>Improved the handling of remote API responses (serialized data can no longer create PHP objects)</li>
            <li>Improved the import result notice, it now says why entries were skipped</li>
            <li>Improved the review modal's compatibility with older addon versions and with plugins whose code fails while it opens</li>
            <li>Improved the review modal: it now uses the browser's native dialog, its frame and close button can no longer be broken by theme styles, and closing it can no longer discard a half-typed form by accident</li>
            <li>Improved the speed of review submission and pagination requests</li>
            <li>Improved the update notice for addons with an expired, inactive, or invalid license</li>
        </ul>

        <h4>🚫 Removed</h4>
        <ul>
            <li>Removed the broken review autosave endpoints from the REST API</li>
        </ul>

        <h4>🐞 Bugs Fixed</h4>
        <ul>
            <li>Fixed a database error when a foreign key references a missing table</li>
            <li>Fixed a failed migration being retried every minute instead of hourly</li>
            <li>Fixed a failed plugin install being retried on every page load</li>
            <li>Fixed a PHP object injection vulnerability in the review form (reported by Jakub Herman)</li>
            <li>Fixed an endless migration loop causing database deadlocks after restoring a backup</li>
            <li>Fixed checkbox settings being emptied by settings updates that did not come from the settings form (WP-CLI, an importer, or another plugin)</li>
            <li>Fixed custom message templates and the multilingual setting being reset by settings updates</li>
            <li>Fixed foreign keys being skipped when a database restore changes a table engine</li>
            <li>Fixed integration role settings (ProfilePress, Ultimate Member) not saving when every checkbox was unchecked</li>
            <li>Fixed review form submissions without JavaScript being ignored on some sites using plain permalinks</li>
            <li>Fixed review text being able to break mid-word when a review is squeezed into a narrow space</li>
            <li>Fixed scheduled background tasks</li>
            <li>Fixed the addon license notice being hidden by a release upgrade notice on the Updates page</li>
            <li>Fixed the "all" data-removal option on uninstall leaving review images and the uploads folder behind</li>
            <li>Fixed the block preview in the editor not updating when some settings are changed</li>
            <li>Fixed the foreign key check matching same-named constraints on other tables</li>
            <li>Fixed the import progress bar counting skipped entries as imported</li>
            <li>Fixed the "Load more" pagination stopping one page early</li>
            <li>Fixed the review form not showing a confirmation message after submission when JavaScript is disabled</li>
            <li>Fixed the revision links in REST API review responses</li>
        </ul>

    </div>
</div>
