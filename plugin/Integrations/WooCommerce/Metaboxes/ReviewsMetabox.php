<?php

namespace GeminiLabs\SiteReviews\Integrations\WooCommerce\Metaboxes;

use GeminiLabs\SiteReviews\Contracts\MetaboxContract;

class ReviewsMetabox implements MetaboxContract
{
    /**
     * @param \WP_Post $post
     */
    public function register($post): void
    {
        if ('product' === get_post_type($post)) {
            $title = _x('Reviews', 'admin-text', 'site-reviews');
            add_meta_box('commentsdiv', $title, [$this, 'render'], 'product', 'normal');
        }
    }

    /**
     * @param \WP_Post $post
     */
    public function render($post): void
    {
        $ratings = glsr_get_ratings([
            'assigned_posts' => $post->ID,
        ]);
        glsr()->render('integrations/woocommerce/metabox-reviews', [
            'postId' => $post->ID,
            'ratings' => $ratings,
        ]);
    }
}
