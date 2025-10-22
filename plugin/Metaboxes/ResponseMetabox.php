<?php

namespace GeminiLabs\SiteReviews\Metaboxes;

use GeminiLabs\SiteReviews\Contracts\MetaboxContract;
use GeminiLabs\SiteReviews\Database\PostMeta;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Sanitizer;
use GeminiLabs\SiteReviews\Review;

class ResponseMetabox implements MetaboxContract
{
    public function register(\WP_Post $post): void
    {
        if (!Review::isEditable($post)) {
            return;
        }
        if (!glsr()->can('respond_to_post', $post->ID)) {
            return;
        }
        $id = glsr()->post_type.'-responsediv';
        $title = _x('Respond Publicly', 'admin-text', 'site-reviews');
        add_meta_box($id, $title, [$this, 'render'], null, 'normal', 'high');
    }

    public function render(\WP_Post $post): void
    {
        wp_nonce_field('response', '_nonce-response', false);
        $response = glsr(PostMeta::class)->get($post->ID, 'response', 'string');
        $response = glsr(Sanitizer::class)->sanitizeTextHtml($response);
        glsr()->render('partials/editor/metabox-response', compact('response'));
    }

    public function save(Review $review): bool
    {
        if (!wp_verify_nonce(Helper::filterInput('_nonce-response'), 'response')) {
            return false;
        }
        return (bool) glsr(ReviewManager::class)->updateResponse($review->ID, [
            'response' => Helper::filterInput('response'),
        ]);
    }
}
