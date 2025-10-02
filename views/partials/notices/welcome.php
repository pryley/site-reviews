<?php defined('ABSPATH') || exit; ?>

<h3>
    <?php echo $icon; ?>
    <?php echo _x('Hello Gorgeous!', 'admin-text', 'site-reviews'); ?>
</h3>
<p>
    <?php printf(
        _x('Thank you for installing %s I really hope you love it! Click me at any time for assistance, I\'m here to help!', 'plugin name (admin-text)', 'site-reviews'),
        glsr()->name
    ); ?>
</p>
<p class="glsr-notice-buttons">
    <a class="components-button is-primary is-small has-icon has-text dashicons-before dashicons-youtube" href="https://youtu.be/H5HdMCXvuq8" target="_blank">
        <?php echo _x("Watch the Tutorial", 'admin-text', 'site-reviews'); ?>
    </a>
    <?php if (glsr()->hasPermission('documentation', 'shortcodes')) { ?>
        <a class="components-button is-secondary is-small" href="<?php echo esc_url(glsr_admin_url('documentation', 'shortcodes')); ?>">
            <?php echo _x('Documentation', 'admin-text', 'site-reviews'); ?>
        </a>
    <?php } ?>
    <button type="button" class="components-button is-tertiary is-small">
        <?php echo _x('Close', 'admin-text', 'site-reviews'); ?>
    </button>
</p>
