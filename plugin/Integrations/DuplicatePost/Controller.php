<?php

namespace GeminiLabs\SiteReviews\Integrations\DuplicatePost;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Review;

class Controller extends AbstractController
{
    /**
     * @param int|\WP_Error $newPostId
     * @param \WP_Post      $post
     *
     * @action duplicate_post_post_copy
     */
    public function duplicateReview($newPostId, $post): void
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
     *
     * @filter bulk_actions-edit-{Application::POST_TYPE}
     */
    public function filterBulkActions($actions): array
    {
        $actions = Arr::consolidate($actions);
        unset($actions['duplicate_post_bulk_rewrite_republish']);
        return $actions;
    }

    /**
     * @param string[] $actions
     * @param \WP_Post $post
     *
     * @filter post_row_actions
     */
    public function filterRowActions($actions, $post): array
    {
        $actions = Arr::consolidate($actions);
        if (Review::isReview($post)) {
            unset($actions['rewrite']);
        }
        return $actions;
    }

    /**
     * @param \WP_Post|null $post
     *
     * @action post_submitbox_start
     */
    public function removeRewriteEditorLink($post): void
    {
        if (!Review::isReview($post)) {
            return;
        }
        global $wp_filter;
        $callbacks = Arr::get($wp_filter, 'post_submitbox_start.callbacks.10', []);
        foreach ($callbacks as $key => $value) {
            if (str_ends_with($key, 'add_rewrite_and_republish_post_button')) {
                remove_action('post_submitbox_start', Arr::get($value, 'function'), 10);
            }
        }
    }
}
