<?php defined('ABSPATH') || exit; ?>

<div class="notice is-dismissible glsr-notice" data-dismiss="welcome">
    <p>
        <?php printf(_x('Thank you for installing %s! I hope you love it.', 'plugin name (admin-text)', 'site-reviews'),
            sprintf('<strong>%s</strong> v%s', glsr()->name, glsr()->version)
        ); ?> ✨
    </p>
    <p class="glsr-notice-buttons">
        <?php if (glsr()->hasPermission('welcome')) { ?>
            <a class="button button-primary" href="<?php echo glsr_admin_url('welcome'); ?>"><?php echo _x("Start Here", 'admin-text', 'site-reviews'); ?></a>
        <?php } ?>
        <?php if (glsr()->hasPermission('documentation', 'shortcodes')) { ?>
            <a class="button button-link" href="<?php echo glsr_admin_url('documentation', 'shortcodes'); ?>"><?php echo _x('Read the Shortcode Documentation', 'admin-text', 'site-reviews'); ?> →</a>
        <?php } ?>
    </p>
</div>
