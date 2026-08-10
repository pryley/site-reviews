<?php defined('WPINC') || exit; ?>

<h2 class="title">SureCart</h2>

<?php /* translators: %s: link with the text "Learn how to" */ echo wp_get_admin_notice(
    sprintf(_x('%s add reviews to your SureCart Shop and Product pages.', 'Learn how to (admin-text)', 'site-reviews'),
        glsr_admin_link('documentation.integrations', _x('Learn how to', 'admin-text', 'site-reviews'), '#integrations-surecart')
    ),
    ['type' => 'info', 'additional_classes' => ['inline']]
); ?>

<table class="form-table">
    <tbody>
        {{ rows }}
    </tbody>
</table>
