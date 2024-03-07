<?php

namespace GeminiLabs\SiteReviews\Metaboxes;

use GeminiLabs\SiteReviews\Contracts\MetaboxContract;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Modules\Sanitizer;
use GeminiLabs\SiteReviews\Review;

class AssignedUsersMetabox implements MetaboxContract
{
    /**
     * @param \WP_Post $post
     */
    public function register($post): void
    {
        if (Review::isReview($post)) {
            $id = glsr()->post_type.'-usersdiv';
            $title = _x('Assigned Users', 'admin-text', 'site-reviews');
            add_meta_box($id, $title, [$this, 'render'], null, 'side');
        }
    }

    /**
     * @param \WP_Post $post
     */
    public function render($post): void
    {
        $review = glsr(ReviewManager::class)->get($post->ID);
        wp_nonce_field('assigned_users', '_nonce-assigned-users', false);
        $templates = array_reduce($review->assigned_users, function ($carry, $userId) {
            $displayName = get_the_author_meta('display_name', $userId);
            $displayName = glsr(Sanitizer::class)->sanitizeUserName($displayName);
            $carry .= glsr(Template::class)->build('partials/editor/assigned-entry', [
                'context' => [
                    'data.id' => $userId,
                    'data.name' => 'user_ids[]',
                    'data.url' => esc_url(get_author_posts_url($userId)),
                    'data.title' => esc_attr($displayName),
                ],
            ]);
            return $carry;
        });
        glsr()->render('partials/editor/metabox-assigned-users', [
            'templates' => $templates,
        ]);
    }
}
