<?php defined('ABSPATH') || exit; ?>

<h3>
    <?php echo $icon; ?>
    <?php echo _x('Can You Help?', 'admin-text', 'site-reviews'); ?>
</h3>
<p>
    <?php echo _x('If you leave a ★★★★★ 5-star review on WordPress, it motivates me to keep making the plugin better and continue providing you free help in the forums.', 'admin-text', 'site-reviews'); ?>
</p>
<p class="glsr-notice-buttons">
    <a class="components-button is-primary is-small" href="https://wordpress.org/support/view/plugin-reviews/site-reviews?filter=5#new-post" target="_blank">
        <?php echo _x('Write a Review', 'admin-text', 'site-reviews'); ?>
    </a>
    <button data-dismiss="interval" type="button" class="components-button is-tertiary is-small">
        <?php echo _x('Not Now', 'admin-text', 'site-reviews'); ?>
    </button>
</p>
