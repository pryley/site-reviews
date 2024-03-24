<?php

namespace GeminiLabs\SiteReviews\Metaboxes;

use GeminiLabs\SiteReviews\Contracts\MetaboxContract;
use GeminiLabs\SiteReviews\Modules\Html\MetaboxForm;
use GeminiLabs\SiteReviews\Review;

class DetailsMetabox implements MetaboxContract
{
    public function register(\WP_Post $post): void
    {
        if (!Review::isReview($post)) {
            return;
        }
        $id = glsr()->post_type.'-detailsdiv';
        $title = _x('Review Details', 'admin-text', 'site-reviews');
        add_meta_box($id, $title, [$this, 'render'], null, 'normal', 'high');
    }

    public function render(\WP_Post $post): void
    {
        $review = glsr_get_review($post->ID);
        glsr()->render('partials/editor/metabox-details', [
            'fields' => (new MetaboxForm($review))->build(),
        ]);
    }
}
