<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox is-fullwidth">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="welcome-v7_1_0">
            <span class="title">Version 7.1</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="welcome-v7_1_0" class="inside">
        <p><em>Release Date &mdash; September 26th, 2024</em></p>

        <h4>✨ New Features</h4>
        <ul>
            <li>Added "Resend Verification Email" button to reviews.</li>
            <li>Added the previous major version to the <?php echo glsr_admin_link('tools.general', 'Rollback', '#tools-rollback-plugin'); ?> tool (i.e. you can now rollback from v7 to v6).</li>
            <li>Updated the addon update checker to use the WordPress update hooks.</li>
            <li>Updated the <?php echo glsr_admin_link('tools.general', 'Export Reviews', '#tools-export-reviews'); ?> tool to support images (<?php echo glsr_premium_link('site-reviews-images'); ?> addon required) and custom fields (<?php echo glsr_premium_link('site-reviews-forms'); ?> addon required).</li>
            <li>Updated the <?php echo glsr_admin_link('tools.general', 'Import Reviews', '#tools-import-reviews'); ?> tool to support images (<?php echo glsr_premium_link('site-reviews-images'); ?> addon required), custom fields (<?php echo glsr_premium_link('site-reviews-forms'); ?> addon required), and skip already imported reviews.</li>
        </ul>

        <h4>🐞 Bugs Fixed</h4>
        <ul>
            <li>Fixed compatibility with the Enfold theme's duplicate post feature.</li>
            <li>Fixed multilingual taxonomy integration.</li>
            <li>Fixed some visual inconsistancies in the admin.</li>
            <li>Fixed the review count in the dashboard widget.</li>
        </ul>
    </div>
</div>
