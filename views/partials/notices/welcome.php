<div class="notice notice-info is-dismissible glsr-notice" data-dismiss="welcome">
    <p><?= $text; ?></p>
    <p>
        <a class="components-button is-secondary" href="<?= admin_url('index.php?page='.glsr()->id.'-welcome#tab-whatsnew'); ?>">✨&nbsp;<?= _x('See what\'s new', 'admin-text', 'site-reviews'); ?></a>
        &nbsp;
        <a class="components-button is-secondary" href="<?= admin_url('index.php?page='.glsr()->id.'-welcome#tab-upgrade-guide'); ?>">📄&nbsp;<?= _x('Read the Upgrade Guide', 'admin-text', 'site-reviews'); ?></a>
    </p>
</div>
