<?php defined('ABSPATH') || exit; ?>

<div class="notice is-dismissible glsr-notice" data-dismiss="upgraded">
    <p>
        <?php printf(_x('Thank you for updating %s to %s! I hope you love the improvements.', 'plugin name|version (admin-text)', 'site-reviews'),
            sprintf('<strong>%s</strong>', glsr()->name),
            sprintf('v%s', glsr()->version)
        ); ?> 🎉
    </p>
    <p class="glsr-notice-buttons">
        <?php if (glsr()->hasPermission('welcome', 'whatsnew')) { ?>
            <a class="button" href="<?php echo glsr_admin_url('welcome', 'whatsnew'); ?>"><?php echo _x("See What's New", 'admin-text', 'site-reviews'); ?></a>
        <?php } ?>
        <?php if (glsr()->hasPermission('welcome', 'upgrade-guide')) { ?>
            <a class="button button-link" href="<?php echo glsr_admin_url('welcome', 'upgrade-guide'); ?>"><?php echo _x('Read the Upgrade Guide', 'admin-text', 'site-reviews'); ?> →</a>
        <?php } ?>
    </p>
</div>
