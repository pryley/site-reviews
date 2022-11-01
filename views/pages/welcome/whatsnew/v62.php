<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox is-fullwidth open">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="true" aria-controls="welcome-v6_2_0">
            <span class="title">Version 6.2</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="welcome-v6_2_0" class="inside">
        <p><em>Initial Release Date &mdash; November 2nd, 2022</em></p>
        <h4>✨ New Features</h4>
        <ul>
            <li>Added a duplicate validator which checks if a review with the same values was already submitted.</li>
            <li>Added support for <a href="https://swiftperformance.io/" target="_blank">Swift Performance</a> plugin</li>
        </ul>
        <h4>📢 Changes</h4>
        <ul>
            <li>Disabled notifications when importing reviews</li>
        </ul>
        <h4>🐞 Bugs Fixed</h4>
        <ul>
            <li>Fixed date sanitizer</li>
            <li>Fixed migrations that update plugin settings</li>
            <li>Fixed PHP warning when excerpts are generated</li>
            <li>Fixed premium notice displaying for licensed users</li>
            <li>Fixed size of button loading animation</li>
            <li>Fixed support for GMT dates when importing reviews</li>
            <li>Fixed the reCAPTCHA integrations</li>
            <li>Fixed the <a data-expand="#tools-rollback-plugin" href="<?= glsr_admin_url('tools'); ?>">Rollback Plugin</a> tool</li>
        </ul>
    </div>
</div>

