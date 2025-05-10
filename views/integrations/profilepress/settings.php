<?php defined('WPINC') || exit; ?>

<h2 class="title">ProfilePress</h2>

<div class="components-notice is-info" style="margin-left:0;">
    <p class="components-notice__content">
    <?php printf(_x('Learn how to display profile ratings in your Member Directory page %shere%s.', 'admin-text', 'site-reviews'),
        sprintf('<a data-expand="#integrations-profilepress" href="%s">', glsr_admin_url('documentation', 'integrations')),
        '</a>'
    ); ?>
    </p>
</div>

<table class="form-table">
    <tbody>
        {{ rows }}
    </tbody>
</table>
