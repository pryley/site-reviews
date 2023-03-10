<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox is-fullwidth">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="welcome-v5_23_0">
            <span class="title">Version 5.23</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="welcome-v5_23_0" class="inside">
        <p><em>Release Date &mdash; April 9th, 2022</em></p>

        <h4>✨ New Features</h4>
        <ul>
            <li>Added an <a data-expand="#tools-export-reviews" href="<?= glsr_admin_url('tools', 'general'); ?>">Export Reviews</a> tool</li>
        </ul>

        <h4>💅🏼 Improved</h4>
        <ul>
            <li>Optimised frontend javascript</li>
        </ul>

        <h4>🐞 Bugs Fixed</h4>
        <ul>
            <li>Fixed custom translated strings</li>
            <li>Fixed loading indication on load more button</li>
            <li>Fixed PHP 5.6 compatibility</li>
            <li>Fixed Rollback feature compatibility with other plugins which use the WordPress Upgrader</li>
            <li>Fixed the filter labels on the All Reviews page, they are now translatable</li>
            <li>Fixed the star rating control when editing a review</li>
            <li>Fixed updating of reviews with custom checkbox and toggle fields</li>
            <li>Fixed validation for fields that are empty and not required</li>
            <li>Fixed WordPress admin theme support</li>
        </ul>
    </div>
</div>
