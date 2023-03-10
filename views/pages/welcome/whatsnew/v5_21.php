<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox is-fullwidth">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="welcome-v5_21_0">
            <span class="title">Version 5.21</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="welcome-v5_21_0" class="inside">
        <p><em>Release Date &mdash; February 20th, 2022</em></p>

        <h4>✨ New Features</h4>
        <ul>
            <li>Added a basic GamiPress integration to award points when someone writes or receives a review. For more advanced GamiPress events, use the new <a href="<?= glsr_admin_url('addons'); ?>">GamiPress Reviews</a> addon.</li>
            <li>Added the <a href="<?= glsr_admin_url('addons'); ?>">GamiPress Reviews</a> addon!</li>
            <li>Added "profile_id" as an accepted value in the "assigned_users" shortcode option</li>
        </ul>

        <h4>🐞 Bugs Fixed</h4>
        <ul>
            <li>Fixed CSS used in form validation</li>
            <li>Fixed validation of custom checkbox fields</li>
        </ul>
    </div>
</div>
