<?php defined('ABSPATH') || exit; ?>

<h3>
    <?php echo $icon; ?>
    <?php echo _x('Can You Help?', 'admin-text', 'site-reviews'); ?>
</h3>
<p>
    <?php echo _x('A ★★★★★ 5-star review from you on WordPress helps Site Reviews grow and encourages me to continue providing support and making it even better!', 'admin-text', 'site-reviews'); ?>
</p>
<p class="glsr-notice-buttons">
    <a class="components-button is-primary is-small" href="https://wordpress.org/support/view/plugin-reviews/site-reviews?filter=5#new-post" target="_blank">
        <?php echo _x('Write a Review', 'admin-text', 'site-reviews'); ?>
    </a>
    <button data-dismiss="interval" type="button" class="components-button is-tertiary is-small">
        <?php echo _x('Not Now', 'admin-text', 'site-reviews'); ?>
    </button>
</p>
