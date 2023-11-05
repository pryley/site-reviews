<?php

namespace GeminiLabs\SiteReviews\Metaboxes;

use GeminiLabs\SiteReviews\Contracts\MetaboxContract;
use GeminiLabs\SiteReviews\Review;

class TaxonomyMetabox implements MetaboxContract
{
    /**
     * @param \WP_Post $post
     */
    public function register($post): void
    {
        // This is done with register_taxonomy
    }

    /**
     * @param \WP_Post $post
     */
    public function render($post): void
    {
        if (Review::isReview($post)) {
            glsr()->render('partials/editor/metabox-categories', [
                'post' => $post,
                'tax_name' => glsr()->taxonomy,
                'taxonomy' => get_taxonomy(glsr()->taxonomy),
            ]);
        }
    }
}
