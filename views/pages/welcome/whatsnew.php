<?php defined('WPINC') || die; ?>

<p class="about-description">We think you'll love the changes in this new release!</p>
<div class="is-fullwidth">
    <div class="glsr-flex-row">
        <div class="glsr-column">
            <div class="card is-fullwidth">
                <h3><?= glsr()->version; ?></h3>
                <p><em>Release Date &mdash; ?? September, 2019</em></p>
                <h4>New Features</h4>
                <ul>
                    <li>Added Multisite support</li>
                    <li>Added product schema price options</li>
                    <li>Added proxy header support for IP detection</li>
                    <li>Added setting to choose which blacklist to use</li>
                    <li>Added setting to limit review submissions</li>
                    <li>Added widget icons in the WordPress customizer</li>
                    <li>Added WPML integration for summary counts</li>
                </ul>
                <h4>Changes</h4>
                <ul>
                    <li>Changed category assignment to one-per-review</li>
                    <li>Removed $_SESSION usage</li>
                </ul>
                <h4>Tweaks</h4>
                <ul>
                    <li>Improved ajax pagination</li>
                    <li>Improved documentation</li>
                    <li>Improved email failure logging</li>
                    <li>Improved internal console usage</li>
                    <li>Improved system info</li>
                    <li>Updated FAQs</li>
                    <li>Updated plugin hooks</li>
                    <li>Updated templates</li>
                </ul>
                <h4>Bugs Fixed</h4>
                <ul>
                    <li>Fixed badge counter in menu when reviews are approved/unapproved</li>
                    <li>Fixed overriding star styles on the "Add plugin" page</li>
                    <li>Fixed per-page limit in the Reviews block</li>
                    <li>Fixed PHP 7.2 support</li>
                    <li>Fixed review counts</li>
                    <li>Fixed review menu counts from changing when approving/unapproving comments</li>
                    <li>Fixed review revert button</li>
                    <li>Fixed star-rating CSS when using the helper function</li>
                    <li>Fixed upgrade process when updating to a major plugin version</li>
                </ul>
            </div>
        </div>
    </div>
</div>
