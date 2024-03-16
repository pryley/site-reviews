<?php

namespace GeminiLabs\SiteReviews\Metaboxes;

use GeminiLabs\SiteReviews\Contracts\MetaboxContract;
use GeminiLabs\SiteReviews\Review;

class TaxonomyMetabox implements MetaboxContract
{
    public function register(\WP_Post $post): void
    {
        // This is done with register_taxonomy
    }

    public function render(\WP_Post $post): void
    {
        if (!Review::isReview($post)) {
            return;
        }
        glsr()->render('partials/editor/metabox-categories', [
            'post' => $post,
            'taxonomy' => get_taxonomy(glsr()->taxonomy),
        ]);
    }
}
