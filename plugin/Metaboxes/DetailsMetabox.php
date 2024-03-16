<?php

namespace GeminiLabs\SiteReviews\Metaboxes;

use GeminiLabs\SiteReviews\Contracts\MetaboxContract;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Modules\Html\MetaboxField;
use GeminiLabs\SiteReviews\Review;

class DetailsMetabox implements MetaboxContract
{
    public function normalize(Review $review): array
    {
        $fields = glsr()->config('forms/metabox-fields');
        if (count(glsr()->retrieveAs('array', 'review_types')) < 2) {
            unset($fields['type']);
        }
        foreach ($fields as $key => &$field) {
            $field['class'] = 'glsr-input-value';
            $field['name'] = $key;
            $field['data-value'] = $review->$key;
            $field['disabled'] = 'add' !== glsr_current_screen()->action;
            $field['review_object'] = $review;
            $field['value'] = $review->$key;
        }
        $fields = glsr()->filterArray('metabox/fields', $fields, $review);
        array_walk($fields, function (&$field) {
            $field = new MetaboxField($field);
        });
        return array_values($fields);
    }

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
        $review = glsr(ReviewManager::class)->get($post->ID);
        glsr()->render('partials/editor/metabox-details', [
            'metabox' => $this->normalize($review),
        ]);
    }
}
