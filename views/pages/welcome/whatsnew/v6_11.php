<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox is-fullwidth open">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="true" aria-controls="welcome-v6_11_0">
            <span class="title">Version 6.11</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="welcome-v6_11_0" class="inside">
        <p><em>Release Date &mdash; October 20th, 2023</em></p>

        <h4>✨ New Features</h4>
        <ul>
            <li>Added review verification requests (replaces the previous manual review verification).</li>
            <li>Added support for the <a target="_blank" href="https://wordpress.org/plugins/nitropack/">Nitropack</a> cache plugin.</li>
            <li>Added tag buttons to notification message setting.</li>
            <li>Added the ability to approve a review directly from a Discord, Email, or Slack notification.</li>
        </ul>

        <h4>📢 Changed</h4>
        <ul>
            <li>Changed number of scheduled actions to 50 per page.</li>
        </ul>

        <h4>🚫 Removed</h4>
        <ul>
            <li>Removed the ability to manually verify reviews (use the <code><a href="<?= glsr_admin_url('settings', 'general'); ?>">Request Verification</a></code> setting instead). To re-enable manual review verification, see the <code><a data-expand="#faq-enable-manual-verification" href="<?= glsr_admin_url('documentation', 'faq'); ?>">FAQ</a></code> page.</li>
        </ul>

        <h4>🐞 Bugs Fixed</h4>
        <ul>
            <li>Fixed conversion (HTML to plain text) of links in notifications.</li>
            <li>Fixed importing of saved strings with the Import Settings tool.</li>
        </ul>
    </div>
</div>

