<?php defined('WPINC') || die; ?>

<div class="glsr-addon-wrap">
    <div class="glsr-addon">
        <a href="{{ link }}" class="glsr-addon-screenshot" data-slug="{{ slug }}">
            <span class="screen-reader-text">{{ title }}</span>
        </a>
        <div class="glsr-addon-description">
            <h3 class="glsr-addon-name">{{ title }}</h3>
            <p>{{ description }}</p>
        </div>
        <div class="glsr-addon-footer">
        <?php if (!is_wp_error(validate_plugin($plugin))) : ?>
            <?php if (is_plugin_active($plugin)) : ?>
            <span class="glsr-addon-link button button-secondary" disabled>
                <?= __('Installed', 'site-reviews'); ?>
            </span>
            <?php else: ?>
            <a href="<?= wp_nonce_url(self_admin_url('plugins.php?action=activate&plugin='.$plugin), 'activate-plugin_'.$plugin); ?>" class="glsr-addon-link button button-secondary">
                <?= __('Activate', 'site-reviews'); ?>
            </a>
            <?php endif; ?>
        <?php elseif (!empty($beta)): ?>
            <a href="mailto:site-reviews@geminilabs.io?subject=I%20would%20like%20to%20become%20a%20beta%20tester%20({{ slug }})" class="glsr-addon-link glsr-external button button-secondary">
                <?= __('Try the beta', 'site-reviews'); ?>
            </a>
        <?php else: ?>
            <a href="{{ link }}" class="glsr-addon-link glsr-external button button-secondary">
                <?= __('More Info', 'site-reviews'); ?>
            </a>
        <?php endif; ?>
        </div>
    </div>
</div>
