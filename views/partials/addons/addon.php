<?php defined('ABSPATH') || exit; ?>

<div class="glsr-addon">
    <a href="{{ url }}" class="glsr-addon-screenshot" data-id="{{ id }}" style="{{ style }}">
        <span class="screen-reader-text">{{ title }}</span>
    </a>
    <div class="glsr-addon-description">
        <h3 class="glsr-addon-name">{{ title }}</h3>
        <p>{{ description }}</p>
    </div>
    <div class="glsr-addon-footer">
    <?php if (!is_wp_error(validate_plugin($plugin))) : ?>
        <?php if (is_plugin_active($plugin)) : ?>
            <?php if (glsr()->addon($id) && glsr()->hasPermission('settings') && !empty(glsr($id)->config('settings'))) : ?>
                <a href="<?php echo glsr_admin_url('settings', 'addons', glsr($id)->slug); ?>" class="glsr-addon-link button button-secondary">
                    <?php echo _x('Settings', 'admin-text', 'site-reviews'); ?>
                </a>
            <?php endif; ?>
            <span class="glsr-addon-link button button-secondary" disabled>
                <?php echo _x('Installed', 'admin-text', 'site-reviews'); ?>
            </span>
        <?php else: ?>
            <a href="<?php echo wp_nonce_url(self_admin_url("plugins.php?action=activate&plugin={$plugin}"), "activate-plugin_{$plugin}"); ?>" class="glsr-addon-link button button-primary">
                <?php echo _x('Activate', 'admin-text', 'site-reviews'); ?>
            </a>
        <?php endif; ?>
    <?php else: ?>
        <a href="{{ url }}" class="glsr-addon-link glsr-external button button-secondary">{{ link_text }}</a>
    <?php endif; ?>
    </div>
</div>
