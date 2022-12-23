<?php defined('ABSPATH') || exit; ?>

<div class="glsr-notice glsr-notice-footer notice-review" data-dismiss="footer">
    <p>
        <?php printf('%s %s 💖',
                sprintf(_x('Are you happy with %s?', 'Site Reviews (admin-text)', 'site-reviews'), sprintf('<strong>%s</strong>', glsr()->name)),
                sprintf(_x('Please rate %s on WordPress and let other people know about it.', '★★★★★ (admin-text)', 'site-reviews'),
                    '<a class="button button-link" href="https://wordpress.org/support/view/plugin-reviews/site-reviews?filter=5#new-post" target="_blank">★★★★★</a>'
                )
            );
        ?>
    </p>
</div>
