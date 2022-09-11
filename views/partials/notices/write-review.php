<?php defined('ABSPATH') || exit; ?>

<div class="notice notice-review glsr-notice" data-dismiss="write-review">
    <p><?= $text; ?></p>
    <p class="glsr-notice-buttons">
        <a class="button button-primary" href="https://wordpress.org/support/view/plugin-reviews/site-reviews?filter=5#new-post" target="_blank"><?= _x('Leave a review', 'admin-text', 'site-reviews'); ?></a>
        <button type="button" class="button glsr-dismiss-link"><?= _x('No, thank you', 'admin-text', 'site-reviews'); ?></button>
    </p>
</div>
