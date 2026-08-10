<?php defined('ABSPATH') || exit; ?>

<h2 class="title"><?php echo _x('License Key Settings', 'admin-text', 'site-reviews'); ?></h2>

<?php if (empty($context['rows'])) { ?>
    <?php /* translators: %s: link with the text "premium addon" */ echo wp_get_admin_notice(
        sprintf(
            _x('You will be able to save your license key here after you install and activate a %s.', 'admin-text', 'site-reviews'),
            glsr_premium_link('addons', _x('premium addon', 'admin-text', 'site-reviews'))
        ),
        ['type' => 'info', 'additional_classes' => ['inline']]
    ); ?>
<?php } else { ?>
    <?php /* translators: %s: link to the License Keys page */ echo wp_get_admin_notice(
        sprintf(
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
<?php } ?>
