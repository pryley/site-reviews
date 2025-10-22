<?php defined('WPINC') || exit; ?>

<h2 class="title">ProfilePress</h2>

<div class="components-notice is-info" style="margin-left:0;">
    <p class="components-notice__content">
    <?php printf(_x('%s display profile ratings in your Member Directory page.', 'Learn how to (admin-text)', 'site-reviews'),
        glsr_admin_link('documentation.integrations', _x('Learn how to', 'admin-text', 'site-reviews'), '#integrations-profilepress')
    ); ?>
    </p>
</div>

<table class="form-table">
    <tbody>
        {{ rows }}
    </tbody>
</table>
