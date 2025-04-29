<?php defined('WPINC') || exit; ?>

<h2 class="title">SureCart</h2>

<div class="components-notice is-info" style="margin-left:0;">
    <p class="components-notice__content">
    <?php printf(_x('Learn how to add reviews to your SureCart Shop and Product pages %shere%s.', 'admin-text', 'site-reviews'),
        sprintf('<a data-expand="#integrations-surecart" href="%s">', glsr_admin_url('documentation', 'integrations')),
        '</a>'
    ); ?>
    </p>
</div>

<table class="form-table">
    <tbody>
        {{ rows }}
    </tbody>
</table>
