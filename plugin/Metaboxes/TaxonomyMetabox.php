<?php

namespace GeminiLabs\SiteReviews\Metaboxes;

use GeminiLabs\SiteReviews\Contracts\MetaboxContract;
use GeminiLabs\SiteReviews\Review;

class TaxonomyMetabox implements MetaboxContract
{
    /**
     * {@inheritdoc}
     */
    public function register($post)
    {
        // This is done with register_taxonomy
    }

    /**
     * {@inheritdoc}
     */
    public function render($post)
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
