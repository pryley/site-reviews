<?php defined('WPINC') || die; ?>

<p class="about-description">We hope you love the changes in this new release!</p>
<div class="is-fullwidth">
    <div class="glsr-flex-row">
        <div class="glsr-column">
            <div class="card is-fullwidth">
                <h3>Version 4.5</h3>
                <p><em>Release Date &mdash; February 24th, 2020</em></p>
                <h4>Changes</h4>
                <ul>
                    <li>Updated the Rebusify integration as they have rebranded to <a href="https://trustalyze.com?ref=105">Trustalyze</a></li>
                </ul>
                <h4>Bugs Fixed</h4>
                <ul>
                    <li>Fixed compatibility with misbehaving plugins that break the Settings tabs</li>
                    <li>Fixed potential errors when upgrading from versions prior to 4.3.8</li>
                    <li>Fixed the "Recalculate Summary Counts" tool to work correctly after pages with assigned reviews are cloned</li>
                </ul>
            </div>
            <div class="card is-fullwidth">
                <h3>Version 4.4</h3>
                <p><em>Release Date &mdash; February 12th, 2020</em></p>
                <h4>New Features</h4>
                <ul>
                    <li>Added an invitation to try the unreleased beta version of the Images add-on</li>
                    <li>Added custom review capabilities which can be added to your user roles. By default, these capabilities will mirror the capabilities of your posts. For example, if a user role has permission to edit others posts, then that role will also have permission to edit other users reviews.</li>
                </ul>
                <h4>Tweaks</h4>
                <ul>
                    <li>Updated the Common Problems and Solutions section on the Help page</li>
                    <li>Updated the FAQ page</li>
                </ul>
                <h4>Bugs Fixed</h4>
                <ul>
                    <li>Fixed compatibility with Fusion Builder</li>
                    <li>Fixed plugin migrations</li>
                </ul>
            </div>
            <div class="card is-fullwidth">
                <h3>Version 4.3</h3>
                <p><em>Release Date &mdash; January 12th, 2020</em></p>
                <h4>New Features</h4>
                <ul>
                    <li>Added migration tool for cases when auto-migration is not triggered when upgrading to version 4</li>
                    <li>Added permission validation to the submission form</li>
                    <li>Added update functionality for add-ons</li>
                </ul>
                <h4>Tweaks</h4>
                <ul>
                    <li>Rebuilt the star rating to account for a MAX_RATING greater than 5</li>
                    <li>Updated the version requirement checks</li>
                </ul>
                <h4>Bugs Fixed</h4>
                <ul>
                    <li>Fixed assigned_to links</li>
                    <li>Fixed avatar regeneration</li>
                    <li>Fixed review helper functions</li>
                    <li>Fixed schema to not show if there are no reviews or ratings</li>
                    <li>Fixed shortcode defaults</li>
                    <li>Fixed summary percentage bars when rating count is empty</li>
                    <li>Fixed summary rating calculation</li>
                    <li>Fixed validation of multi-value fields</li>
                </ul>
            </div>
            <div class="card is-fullwidth">
                <h3>Version 4.2</h3>
                <p><em>Release Date &mdash; October 29th, 2019</em></p>
                <h4>New Features</h4>
                <ul>
                    <li>Added the <code>site-reviews/support/deprecated/v4</code> filter hook. If this hook returns <code>false</code> then the plugin will skip deprecated checks. If the plugin console does not show any deprecated notices, then it should be safe to use this hook for increased performance.</li>
                    <li>Added WordPress v5.3 compatibility</li>
                </ul>
                <h4>Tweaks</h4>
                <ul>
                    <li>Optimised translation performance</li>
                    <li>Rebuilt the WordPress Editor Blocks</li>
                </ul>
                <h4>Bugs Fixed</h4>
                <ul>
                    <li>Fixed pagination of reviews on static front page</li>
                    <li>Fixed performance issues related to IP Address detection</li>
                    <li>Fixed potential SSL error when fetching Cloudflare IP ranges</li>
                    <li>Fixed System Info when ini_get() function is disabled</li>
                </ul>
            </div>
            <div class="card is-fullwidth">
                <h3>Version 4.1</h3>
                <p><em>Release Date &mdash; October 16th, 2019</em></p>
                <h4>New Features</h4>
                <ul>
                    <li>Added optional "Email", "IP Address", and "Response" columns to the reviews table</li>
                </ul>
                <h4>Changes</h4>
                <ul>
                    <li>Changed <code>[site_reviews]</code> "count" option name to "display" (i.e. [site_reviews display=10])</li>
                    <li>Changed <code>glsr_get_reviews()</code> "count" option name to "per_page" (i.e. glsr_get_reviews(['per_page' => 10]))</li>
                    <li>Renamed "Documentation" page to "Help"</li>
                </ul>
                <h4>Tweaks</h4>
                <ul>
                    <li>Updated the "Common Problems and Solutions" help section</li>
                </ul>
                <h4>Bugs Fixed</h4>
                <ul>
                    <li>Fixed a HTML5 validation issue in the plugin settings</li>
                    <li>Fixed column sorting on the reviews table</li>
                    <li>Fixed email template tags</li>
                    <li>Fixed IP address detection for servers that do not support IPv6</li>
                    <li>Fixed pagination links from triggering in the editor block</li>
                    <li>Fixed pagination when using the default count of 5 reviews per page</li>
                    <li>Fixed pagination with hidden review fields</li>
                    <li>Fixed PHP compatibility issues</li>
                    <li>Fixed plugin migration on update</li>
                    <li>Fixed plugin uninstall</li>
                    <li>Fixed translations for default text that include a HTML link</li>
                </ul>
            </div>
            <div class="card is-fullwidth">
                <h3>Version 4.0</h3>
                <p><em>Release Date &mdash; October 6th, 2019</em></p>
                <h4>New Features</h4>
                <ul>
                    <li>Added Multisite support</li>
                    <li>Added product schema price options</li>
                    <li>Added proxy header support for IP detection</li>
                    <li>Added <a href="https://trustalyze.com?ref=105" target="_blank">Rebusify Confidence System</a> integration for blockchain verification of reviews</li>
                    <li>Added setting to choose the name format of the review author</li>
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
                    <li>Improved AJAX pagination</li>
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
