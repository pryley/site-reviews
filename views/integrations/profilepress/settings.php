<?php defined('WPINC') || exit; ?>

<h2 class="title">ProfilePress</h2>

<?php /* translators: %s: link with the text "Learn how to" */ echo wp_get_admin_notice(
    sprintf(_x('%s display profile ratings in your Member Directory page.', 'Learn how to (admin-text)', 'site-reviews'),
        glsr_admin_link('documentation.integrations', _x('Learn how to', 'admin-text', 'site-reviews'), '#integrations-profilepress')
    ),
    ['type' => 'info', 'additional_classes' => ['inline']]
); ?>

<table class="form-table">
    <tbody>
        {{ rows }}
    </tbody>
</table>
