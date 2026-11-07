<?php defined('ABSPATH') || exit; ?>

<div id="glsr-dp-overlay"></div>
<template id="glsr-deactivate-dialog">
    <dialog class="glsr-dp-overlay" aria-label="<?php echo esc_attr_x('Deactivation Details', 'admin-text', 'site-reviews'); ?>">
        <div class="glsr-dp-wrap" role="document">
            <div class="glsr-dp-header">
                <h3 data-title="<?php /* translators: %s: plugin name */ echo esc_attr_x('Deactivating %s', 'Plugin name (admin-text)', 'site-reviews'); ?>"></h3>
                <button type="button" class="close dashicons dashicons-no">
                    <span class="screen-reader-text"><?php echo esc_html_x('Close deactivation dialog', 'admin-text', 'site-reviews'); ?></span>
                </button>
            </div>
            <div class="glsr-dp-content">
                <p><?php echo esc_html_x('If you have a moment, please tell me how we can improve.', 'admin-text', 'site-reviews'); ?></p>
                <ul class="glsr-dp-reasons">
                    <template>
                        <li>
                            <label class="glsr-dp-reason" data-placeholder="" tabindex="0">
                                <input type="radio" name="reason" value="">
                                <span class="glsr-dp-icon"></span>
                                <span class="glsr-dp-reason"></span>
                            </label>
                        </li>
                    </template>
                </ul>
                <div class="glsr-dp-help" hidden>
                    <?php
                        echo wp_get_admin_notice(
                            /* translators: %s: link with the text "Getting Started" */
                            sprintf(_x('Did you read the %s guide?', 'Getting Started (admin-text)', 'site-reviews'),
                                glsr_admin_link('welcome', _x('Getting Started', 'admin-text', 'site-reviews'))
                            ),
                            ['type' => 'warning', 'additional_classes' => ['inline', 'is-warning']]
                        );
                    ?>
                    <?php if (!glsr(\GeminiLabs\SiteReviews\Licensing\License::class)->isPremium()) { ?>
                        <?php
                            echo wp_get_admin_notice(
                                /* translators: %s: link with the text "addons" */
                                sprintf(_x('Maybe one of the %s provide this feature.', 'link to addons page (admin-text)', 'site-reviews'),
                                    glsr_premium_link('addons', _x('addons', 'admin-text', 'site-reviews'))
                                ),
                                ['type' => 'info', 'additional_classes' => ['inline', 'is-info']]
                            );
                        ?>
                    <?php } ?>
                </div>
                <div class="glsr-dp-details" hidden>
                    <textarea name="details" placeholder="" rows="3"></textarea>
                </div>
                <p>
                    <button type="button" class="button-link expand-info" aria-controls="glsr-dp-info" aria-expanded="false"><?php echo esc_html_x('Click here', 'admin-text', 'site-reviews'); ?></button>
                    <?php /* translators: %s: a link with the text "Click here" */ echo esc_html(trim(sprintf(_x('%s to see the additional data that will be submitted. This non-sensitive data will help me troubleshoot problems and make improvements to the plugin.', 'Click here (admin-text)', 'site-reviews'), ''))); ?>
                </p>
                <div id="glsr-dp-info" class="glsr-dp-info" hidden>
                    <table class="widefat striped" role="presentation">
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="glsr-dp-footer">
                <a href="#" class="components-button deactivate"><?php echo esc_html_x('Skip & Deactivate', 'admin-text', 'site-reviews'); ?></a>
                <button type="button" class="components-button is-primary submit" data-processing="<?php echo esc_attr_x('Processing...', 'admin-text', 'site-reviews'); ?>"><?php echo esc_html_x('Submit & Deactivate', 'admin-text', 'site-reviews'); ?></button>
            </div>
        </div>
    </dialog>
</template>
