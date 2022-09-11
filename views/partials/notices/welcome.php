<?php defined('ABSPATH') || exit; ?>

<div class="notice is-dismissible glsr-notice" data-dismiss="welcome">
    <p><?= $text; ?></p>
    <p class="glsr-notice-buttons">
        <?php if ($fresh) { ?>
            <a class="button" href="<?= glsr_admin_url('documentation', 'support'); ?>" data-expand="#support-get-started"><?= _x("Start Here", 'admin-text', 'site-reviews'); ?></a>
            <a class="button button-link" href="<?= glsr_admin_url('documentation', 'shortcodes'); ?>"><?= _x('Read the Shortcode Documentation', 'admin-text', 'site-reviews'); ?> →</a>
        <?php } else { ?>
            <a class="button" href="<?= glsr_admin_url('welcome', 'whatsnew'); ?>"><?= _x("See What's New", 'admin-text', 'site-reviews'); ?></a>
            <a class="button button-link" href="<?= glsr_admin_url('welcome', 'upgrade-guide'); ?>"><?= _x('Read the Upgrade Guide', 'admin-text', 'site-reviews'); ?> →</a>
        <?php } ?>
    </p>
</div>
