<?php

namespace GeminiLabs\SiteReviews\Metaboxes;

use GeminiLabs\SiteReviews\Contracts\MetaboxContract;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Modules\Sanitizer;
use GeminiLabs\SiteReviews\Review;

class AssignedUsersMetabox implements MetaboxContract
{
    public function register(\WP_Post $post): void
    {
        if (!Review::isReview($post)) {
            return;
        }
        $id = glsr()->post_type.'-usersdiv';
        $title = _x('Assigned Users', 'admin-text', 'site-reviews');
        add_meta_box($id, $title, [$this, 'render'], null, 'side');
    }

    public function render(\WP_Post $post): void
    {
        $review = glsr(ReviewManager::class)->get($post->ID);
        wp_nonce_field('assigned_users', '_nonce-assigned-users', false);
        $templates = array_reduce($review->assigned_users, function ($carry, $userId) {
            $user = get_userdata($userId);
            if (!$user) {
                return $carry;
            }
            $name = glsr(Sanitizer::class)->sanitizeUserName(
                $user->display_name,
                $user->user_nicename
            );
            return $carry.glsr(Template::class)->build('partials/editor/assigned-entry', [
                'context' => [
                    'data.id' => $userId,
                    'data.name' => 'user_ids[]',
                    'data.url' => esc_url(get_author_posts_url($userId)),
                    'data.title' => esc_attr("{$name} ({$user->user_nicename})"),
                ],
            ]);
        }, '');
        glsr()->render('partials/editor/metabox-assigned-users', [
            'templates' => $templates,
        ]);
    }
}
