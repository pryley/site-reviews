<?php defined('ABSPATH') || exit; ?>

<div style="padding: 0 12px;">
    <p style="margin: 1em 0;" class="inline notice notice-info">
        <?php echo _x('Your product reviews are being managed by Site Reviews.', 'admin-text', 'site-reviews'); ?>
    </p>
    <p style="padding: 0; margin: 1em 0;">
        <?php if (0 === $ratings->reviews) { ?>
            <?php echo _x('This product has no reviews.', 'admin-text', 'site-reviews'); ?>
        <?php } else { ?>
            <a href="<?php echo add_query_arg('assigned_post', $postId, glsr_admin_url()); ?>" class="button">
                <?php printf(
                    _nx('View %s product review', 'View %s product reviews', $ratings->reviews, 'admin-text', 'site-reviews'),
                    number_format_i18n($ratings->reviews)
                ); ?>
            </a>
        <?php } ?>
    </p>
</div>
