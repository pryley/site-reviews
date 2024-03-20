<?php defined('WPINC') || exit; ?>

<div class="card" style="
    background-color: #fef8ee;
    border-color: #f0b849;
    margin-top:-1px;
    position:relative;
    z-index:1;
">
    <h3 style="margin-top:0;">Please deactivate Site Reviews before migrating your website.</h3>
    <p>Site Reviews adds foreign key constraints to its custom database tables to make database queries perform much faster.</p>
    <p>Unfortunately, Flywheel does not migrate foreign key constraints correctly; you will need to deactivate Site Reviews before performing the migration to ensure that your reviews are migrated successfully.</p>
    <p>Once you have finished migrating your site, you may reactivate Site Reviews.</p>
</div>
