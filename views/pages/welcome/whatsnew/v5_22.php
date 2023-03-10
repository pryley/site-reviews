<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox is-fullwidth">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="welcome-v5_22_0">
            <span class="title">Version 5.22</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="welcome-v5_22_0" class="inside">
        <p><em>Release Date &mdash; March 24th, 2022</em></p>

        <h4>✨ New Features</h4>
        <ul>
            <li>Added a debug option to the shortcodes</li>
            <li>Added <a data-expand="#support-get-started" href="<?= glsr_admin_url('documentation', 'support'); ?>">Getting Started</a> videos to the Help page</li>
            <li>Added the ability to rollback Site Reviews to a previous version</li>
        </ul>

        <h4>💅🏼 Improved</h4>
        <ul>
            <li>Updated the FAQ Help page</li>
        </ul>

        <h4>🐞 Bugs Fixed</h4>
        <ul>
            <li>Fixed compatibility with the WP Fastest Cache plugin (to clear the current page cache when a review is submitted)</li>
            <li>Fixed expanding excerpts when using loadmore pagination</li>
            <li>Fixed performance on websites with thousands of users</li>
            <li>Fixed submit button loading indicator on Firefox</li>
            <li>Fixed the "Heads up! WPForms has detected an issue..." notice when using the WPForms plugin style</li>
        </ul>
    </div>
</div>
