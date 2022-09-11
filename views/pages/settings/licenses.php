<?php defined('ABSPATH') || exit; ?>

<h2 class="title"><?= _x('Add-on Licenses', 'admin-text', 'site-reviews'); ?></h2>

<div class="components-notice is-info" style="margin-left:0;">
    <p class="components-notice__content">

        <?= sprintf(_x('You need to authorize your website with the license before you can save it here! To do this, go to the %sLicense Keys%s page on your Nifty Plugins account and click the "Manage Sites" button.', 'admin-text', 'site-reviews'), '<a href="https://niftyplugins.com/account/license-keys/">', '</a>'); ?>
    </p>
</div>

<table class="form-table">
    <tbody>
        {{ rows }}
    </tbody>
</table>
