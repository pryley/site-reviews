<div class="notice notice-info is-dismissible glsr-notice" data-dismiss="welcome">
    <p><?= $text; ?></p>
    <p>
        <a class="components-button is-secondary is-small" href="<?= glsr_admin_url('welcome', 'whatsnew'); ?>">✨&nbsp;<?= _x('See What\'s New', 'admin-text', 'site-reviews'); ?></a>
        &nbsp;
        <a href="<?= glsr_admin_url('welcome', 'upgrade-guide'); ?>"><?= _x('Read the upgrade guide', 'admin-text', 'site-reviews'); ?> →</a>
    </p>
</div>
