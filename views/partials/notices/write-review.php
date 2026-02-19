<?php defined('ABSPATH') || exit; ?>

<h3>
    <?php echo $icon; ?>
    <?php echo _x('Can You Help?', 'admin-text', 'site-reviews'); ?>
</h3>
<p>
    <?php echo _x('Please rate Site Reviews 5-stars ★★★★★ on WordPress! Your support motivates me to keep improving the plugin and staying active in the forums. I promise I won’t let it go to my head...much.', 'admin-text', 'site-reviews'); ?>
</p>
<p class="glsr-notice-buttons">
    <a class="components-button is-primary is-small" href="https://wordpress.org/support/view/plugin-reviews/site-reviews?filter=5#new-post" target="_blank">
        <?php echo _x('Write a Review', 'admin-text', 'site-reviews'); ?>
    </a>
    <button data-dismiss="interval" type="button" class="components-button is-tertiary is-small">
        <?php echo _x('Not Now', 'admin-text', 'site-reviews'); ?>
    </button>
</p>
