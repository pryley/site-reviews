<?php defined('ABSPATH') || exit; ?>

<div id="glsr-premium-license" class="glsr-premium-license" data-state="<?php echo esc_attr($state); ?>">
    <h2 class="title"><?php echo _x('Site Reviews Premium', 'admin-text', 'site-reviews'); ?></h2>
    <?php if ('' !== $notice['text']) { ?>
        <?php echo wp_get_admin_notice($notice['text'], ['type' => $notice['type'], 'additional_classes' => ['inline']]); ?>
    <?php } ?>
    <table class="form-table">
        <tbody>
            <tr class="glsr-setting-field" data-field="settings.licenses.site-reviews-premium">
                <th scope="row"><?php echo $label; ?></th>
                <td>
                    <div class="glsr-premium-license__control">
                        <?php echo $field; ?>
                        <?php if (1 === $state) { ?>
                            <button type="button" class="glsr-button button button-primary" data-action="verify" data-loading="<?php echo esc_attr_x('Verifying, please wait...', 'admin-text', 'site-reviews'); ?>">
                                <?php echo _x('Verify License Key', 'admin-text', 'site-reviews'); ?>
                            </button>
                            <?php if ($can_delete) { ?>
                                <button type="button" class="glsr-button button" data-action="delete" data-loading="<?php echo esc_attr_x('Deleting, please wait...', 'admin-text', 'site-reviews'); ?>">
                                    <?php echo _x('Delete', 'admin-text', 'site-reviews'); ?>
                                </button>
                            <?php } ?>
                        <?php } else { ?>
                            <button type="button" class="glsr-button button" data-action="deactivate" data-loading="<?php echo esc_attr_x('Deactivating, please wait...', 'admin-text', 'site-reviews'); ?>">
                                <?php echo _x('Deactivate', 'admin-text', 'site-reviews'); ?>
                            </button>
                            <?php if (4 === $state) { ?>
                                <a href="<?php echo esc_url(glsr_admin_url('premium')); ?>" class="button button-primary">
                                    <?php echo _x('Features', 'admin-text', 'site-reviews'); ?>
                                </a>
                            <?php } elseif (3 === $state) { ?>
                                <a href="<?php echo $activate_url; // escaped by wp_nonce_url() ?>" class="button button-primary">
                                    <?php echo _x('Activate Site Reviews Premium', 'admin-text', 'site-reviews'); ?>
                                </a>
                            <?php } elseif ($can_install) { ?>
                                <button type="button" class="glsr-button button button-primary" data-action="install" data-loading="<?php echo esc_attr_x('Connecting, please wait...', 'admin-text', 'site-reviews'); ?>">
                                    <?php echo _x('Install Site Reviews Premium', 'admin-text', 'site-reviews'); ?>
                                </button>
                            <?php } ?>
                        <?php } ?>
                    </div>
                    <?php if ('' !== $message['text'] && 'description' === $message['type']) { ?>
                        <p class="description glsr-premium-license__message"><?php echo wp_kses_post($message['text']); ?></p>
                    <?php } elseif ('' !== $message['text']) { ?>
                        <?php echo wp_get_admin_notice($message['text'], ['type' => $message['type'], 'additional_classes' => ['inline', 'glsr-premium-license__message']]); ?>
                    <?php } ?>
                    <div class="glsr-premium-license__notice notice inline" role="alert" hidden></div>
                </td>
            </tr>
        </tbody>
    </table>
</div>
