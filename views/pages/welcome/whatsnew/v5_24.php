<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox is-fullwidth">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="welcome-v5_24_0">
            <span class="title">Version 5.24</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="welcome-v5_24_0" class="inside">
        <p><em>Release Date &mdash; May 26th, 2022</em></p>

        <h4>✨ New Features</h4>
        <ul>
            <li>Added the ability to use post type names in the assigned_posts option. This allows you to display all reviews for a specific post type.</li>
        </ul>

        <h4>🐞 Bugs Fixed</h4>
        <ul>
            <li>Fixed AJAX pagination of filtered reviews (when using the Review Filters addon)</li>
            <li>Fixed compatibility with Divi Dynamic CSS</li>
            <li>Fixed compatibility with Oxygen Builder</li>
            <li>Fixed expanding excerpts</li>
            <li>Fixed Export Tool</li>
            <li>Fixed functionality of the assigned_posts option when used with the reviews and summary shortcodes: If the assigned_posts value is invalid, no reviews will be returned</li>
            <li>Fixed PHP error when importing reviews</li>
            <li>Fixed terms toggle when it's not required in the settings</li>
        </ul>
    </div>
</div>
