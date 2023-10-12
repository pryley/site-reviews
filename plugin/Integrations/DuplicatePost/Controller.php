<?php

namespace GeminiLabs\SiteReviews\Integrations\DuplicatePost;

use GeminiLabs\SiteReviews\Controllers\Controller as BaseController;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Review;

class Controller extends BaseController
{
    /**
     * @param int|\WP_Error $newPostId
     * @param \WP_Post $post
     * @action duplicate_post_post_copy
     */
    public function duplicateReview($newPostId, $post)
    {
        if (!Review::isReview($post)) {
            return;
        }
        if (is_wp_error($newPostId)) {
            return;
        }
        $review = glsr_get_review($post->ID);
        if ($review->isValid()) {
            glsr(ReviewManager::class)->createFromPost((int) $newPostId, $review->toArray());
        }
    }

    /**
     * @param string[] $actions
     * @return array
     * @filter bulk_actions-edit-{Application::POST_TYPE}
     */
    public function removeRewriteBulkAction($actions)
    {
        unset($actions['duplicate_post_bulk_rewrite_republish']);
        return $actions;
    }

    /**
     * @param \WP_Post|null $post
     * @action post_submitbox_start
     */
    public function removeRewriteEditorLink($post)
    {
        if (!Review::isReview($post)) {
            return;
        }
        global $wp_filter;
        $callbacks = Arr::get($wp_filter, 'post_submitbox_start.callbacks.10', []);
        foreach ($callbacks as $key => $value) {
            if (Str::endsWith($key, 'add_rewrite_and_republish_post_button')) {
                remove_action('post_submitbox_start', Arr::get($value, 'function'), 10);
            }
        }
    }

    /**
     * @param string[] $actions
     * @param \WP_Post $post
     * @return array
     * @filter post_row_actions
     */
    public function removeRewriteRowAction($actions, $post)
    {
        if (Review::isReview($post)) {
            unset($actions['rewrite']);
        }
        return $actions;
    }
}
