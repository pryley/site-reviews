<?php defined('ABSPATH') || exit; ?>

<div class="notice notice-review glsr-notice" data-dismiss="write-review">
    <p>
        <?php printf('%s %s 💖',
                sprintf(_x('Are you happy with %s?', 'Site Reviews (admin-text)', 'site-reviews'), sprintf('<strong>%s</strong>', glsr()->name)),
                sprintf(_x('Please rate %s on WordPress and let other people know about it.', '★★★★★ (admin-text)', 'site-reviews'), '★★★★★')
            );
        ?>
    </p>
    <p class="glsr-notice-buttons">
        <a class="button button-primary" href="https://wordpress.org/support/view/plugin-reviews/site-reviews?filter=5#new-post" target="_blank"><?php echo _x('Leave a review', 'admin-text', 'site-reviews'); ?></a>
        <button type="button" class="button glsr-dismiss-link"><?php echo _x('No, thank you', 'admin-text', 'site-reviews'); ?></button>
    </p>
</div>
