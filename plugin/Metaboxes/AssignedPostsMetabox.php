<?php

namespace GeminiLabs\SiteReviews\Metaboxes;

use GeminiLabs\SiteReviews\Contracts\MetaboxContract;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Review;

class AssignedPostsMetabox implements MetaboxContract
{
    /**
     * @param \WP_Post $post
     */
    public function register($post): void
    {
        if (Review::isReview($post)) {
            $id = glsr()->post_type.'-postsdiv';
            $title = _x('Assigned Posts', 'admin-text', 'site-reviews');
            add_meta_box($id, $title, [$this, 'render'], null, 'side');
        }
    }

    /**
     * @param \WP_Post $post
     */
    public function render($post): void
    {
        $review = glsr(ReviewManager::class)->get($post->ID);
        wp_nonce_field('assigned_posts', '_nonce-assigned-posts', false);
        $templates = array_reduce($review->assigned_posts, function ($carry, $postId) {
            return $carry.glsr(Template::class)->build('partials/editor/assigned-entry', [
                'context' => [
                    'data.id' => $postId,
                    'data.name' => 'post_ids[]',
                    'data.url' => (string) get_permalink($postId),
                    'data.title' => Helper::ifEmpty(get_the_title($postId), __('(no title)', 'site-reviews')),
                ],
            ]);
        });
        glsr()->render('partials/editor/metabox-assigned-posts', [
            'templates' => $templates,
        ]);
    }
}
