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
     * @param \WP_Post $post
     */
    public function register($post): void
    {
        if (Review::isEditable($post) && glsr()->can('respond_to_post', $post->ID)) {
            $id = glsr()->post_type.'-responsediv';
            $title = _x('Respond Publicly', 'admin-text', 'site-reviews');
            add_meta_box($id, $title, [$this, 'render'], null, 'normal', 'high');
        }
    }

    /**
     * @param \WP_Post $post
     */
    public function render($post): void
    {
        wp_nonce_field('response', '_nonce-response', false);
        glsr()->render('partials/editor/metabox-response', [
            'response' => glsr(Database::class)->meta($post->ID, 'response'),
        ]);
    }

    public function save(Review $review): bool
    {
        if (!wp_verify_nonce(Helper::filterInput('_nonce-response'), 'response')) {
            return false;
        }
        return glsr(ReviewManager::class)->updateResponse($review->ID, [
            'response' => Helper::filterInput('response'),
        ]);
    }
}
