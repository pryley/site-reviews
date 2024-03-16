<?php

namespace GeminiLabs\SiteReviews\Metaboxes;

use GeminiLabs\SiteReviews\Contracts\MetaboxContract;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Review;

class AssignedPostsMetabox implements MetaboxContract
{
    public function register(\WP_Post $post): void
    {
        if (!Review::isReview($post)) {
            return;
        }
        $id = glsr()->post_type.'-postsdiv';
        $title = _x('Assigned Posts', 'admin-text', 'site-reviews');
        add_meta_box($id, $title, [$this, 'render'], null, 'side');
    }

    public function render(\WP_Post $post): void
    {
        $review = glsr(ReviewManager::class)->get($post->ID);
        wp_nonce_field('assigned_posts', '_nonce-assigned-posts', false);
        $templates = array_reduce($review->assigned_posts, function ($carry, $postId) {
            $title = Helper::ifEmpty(get_the_title($postId), __('(no title)', 'site-reviews'));
            return $carry.glsr(Template::class)->build('partials/editor/assigned-entry', [
                'context' => [
                    'data.id' => $postId,
                    'data.name' => 'post_ids[]',
                    'data.url' => esc_url((string) get_permalink($postId)),
                    'data.title' => esc_html($title),
                ],
            ]);
        }, '');
        glsr()->render('partials/editor/metabox-assigned-posts', [
            'templates' => $templates,
        ]);
    }
}
