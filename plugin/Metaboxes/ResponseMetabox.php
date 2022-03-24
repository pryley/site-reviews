<?php

namespace GeminiLabs\SiteReviews\Metaboxes;

use GeminiLabs\SiteReviews\Contracts\MetaboxContract;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Review;

class ResponseMetabox implements MetaboxContract
{
    /**
     * {@inheritdoc}
     */
    public function register($post)
    {
        if (Review::isEditable($post) && glsr()->can('respond_to_post', $post->ID)) {
            $id = glsr()->post_type.'-responsediv';
            $title = _x('Respond Publicly', 'admin-text', 'site-reviews');
            add_meta_box($id, $title, [$this, 'render'], null, 'normal', 'high');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function render($post)
    {
        wp_nonce_field('response', '_nonce-response', false);
        glsr()->render('partials/editor/metabox-response', [
            'response' => glsr(Database::class)->meta($post->ID, 'response'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function save(Review $review)
    {
        if (wp_verify_nonce(Helper::filterInput('_nonce-response'), 'response')) {
            $response = strval(Helper::filterInput('response'));
            return glsr(ReviewManager::class)->updateResponse($review->ID, $response);
        }
    }
}
