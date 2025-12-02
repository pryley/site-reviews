<?php defined('ABSPATH') || exit; ?>

<h3>
    <?php echo $icon; ?>
    <?php echo _x('Thanks for Updating!', 'admin-text', 'site-reviews'); ?>
</h3>
<p>
    <?php printf(_x('%s has been updated to version %s', 'plugin name|version (admin-text)', 'site-reviews'),
        sprintf('<strong>%s</strong>', glsr()->name),
        sprintf('<strong>%s</strong>', glsr()->version)
    ); ?>
</p>
<p>
    <?php echo _x('This is a significant update with many new features, improvements, and essential bug fixes!', 'admin-text', 'site-reviews'); ?>
</p>
<p>
    <?php echo _x('If you are using any code snippets to customise Site Reviews, please read the upgrade guide to ensure that everything continues to work as expected.', 'admin-text', 'site-reviews'); ?>
</p>
<p class="glsr-notice-buttons">
    <?php if (glsr()->hasPermission('welcome', 'whatsnew')) { ?>
        <a class="components-button is-primary is-small" href="<?php echo esc_url(glsr_admin_url('welcome', 'whatsnew')); ?>">
            <?php echo _x("What's New", 'admin-text', 'site-reviews'); ?>
        </a>
    <?php } ?>
    <?php if (glsr()->hasPermission('welcome', 'upgrade-guide')) { ?>
        <a class="components-button is-secondary is-small" href="<?php echo esc_url(glsr_admin_url('welcome', 'upgrade-guide')); ?>">
            <?php echo _x('Upgrade Guide', 'admin-text', 'site-reviews'); ?>
        </a>
    <?php } ?>
    <button type="button" class="components-button is-tertiary is-small">
        <?php echo _x('Close', 'admin-text', 'site-reviews'); ?>
    </button>
</p>
