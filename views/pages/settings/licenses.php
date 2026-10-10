<?php defined('ABSPATH') || exit; ?>

<h2 class="title"><?php echo _x('License Key Settings', 'admin-text', 'site-reviews'); ?></h2>

<?php echo wp_get_admin_notice(
    sprintf(
        /* translators: %s: link to the License Keys page */
        _x('To change the website associated with your license key, go to the %s page and click the "Manage Sites" button.', 'link to License Keys page (admin-text)', 'site-reviews'),
        glsr_premium_link('license-keys')
    ),
    ['type' => 'info', 'additional_classes' => ['inline']]
); ?>
<table class="form-table">
    <tbody>
        {{ rows }}
    </tbody>
</table>
