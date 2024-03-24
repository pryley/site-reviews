<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Defaults\RevisionFieldsDefaults;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Rating;
use GeminiLabs\SiteReviews\Review;

class RevisionController extends AbstractController
{
    /**
     * @filter wp_save_post_revision_check_for_changes
     */
    public function filterCheckForChanges(bool $performCheck, \WP_Post $lastRevision, \WP_Post $post): bool
    {
        return !Review::isReview($post)
            ? $performCheck
            : true;
    }

    /**
     * @filter wp_save_post_revision_post_has_changed
     */
    public function filterReviewHasChanged(bool $hasChanged, \WP_Post $lastRevision, \WP_Post $post): bool
    {
        if (!Review::isReview($post)) {
            return $hasChanged;
        }
        $review = glsr(ReviewManager::class)->get($post->ID, true); // bypass the cache
        $revision = glsr(Database::class)->meta($lastRevision->ID, 'review');
        foreach ($revision as $key => $value) {
            if ((string) $review->$key !== (string) $value) {
                return true;
            }
        }
        return $hasChanged;
    }

    /**
     * @param array[]        $return
     * @param \WP_Post|false $compareFrom
     *
     * @return array[]
     *
     * @filter wp_get_revision_ui_diff
     */
    public function filterRevisionUiDiff(array $return, $compareFrom, \WP_Post $compareTo): array
    {
        if (!Review::isReview($compareTo->post_parent)) {
            return $return;
        }
        $fields = glsr(RevisionFieldsDefaults::class)->defaults();
        $oldReview = $this->reviewFromRevision($compareFrom);
        $newReview = $this->reviewFromRevision($compareTo);
        foreach ($fields as $field => $name) {
            $old = $oldReview->$field;
            $new = $newReview->$field;
            $diff = wp_text_diff($old, $new, [
                'show_split_view' => true,
            ]);
            if ('rating' === $field) {
                $callback = fn ($matches) => $this->ratingValueForDiff((int) $matches[1]);
                $diff = preg_replace_callback('|(\d)</td>|', $callback, $diff);
            }
            if ($diff) {
                $return[] = [
                    'diff' => $diff,
                    'id' => $field,
                    'name' => $name,
                ];
            }
        }
        return $return;
    }

    /**
     * @action wp_restore_post_revision
     */
    public function restoreRevision(int $reviewId, int $revisionId): void
    {
        if (!Review::isReview($reviewId)) {
            return;
        }
        $revision = glsr(Database::class)->meta($revisionId, 'review');
        if (is_array($revision)) {
            glsr(ReviewManager::class)->updateRating($reviewId, $revision);
        }
    }

    /**
     * @action _wp_put_post_revision
     */
    public function saveRevision(int $revisionId): void
    {
        $postId = wp_is_post_revision($revisionId);
        if (Review::isReview($postId)) {
            $review = glsr(ReviewManager::class)->get((int) $postId);
            $revision = glsr(RevisionFieldsDefaults::class)->defaults();
            foreach ($revision as $field => &$value) {
                $value = $review->$field;
            }
            glsr(Database::class)->metaSet($revisionId, 'review', $revision);
        }
    }

    protected function ratingValueForDiff(int $rating): string
    {
        $max = glsr()->constant('MAX_RATING', Rating::class);
        $empty = max(0, $max - $rating);
        $stars = str_repeat('★', $rating).str_repeat('☆', $empty);
        return glsr(Builder::class)->span([
            'style' => 'font-family:system-ui;font-size:16px;line-height:1.375;',
            'text' => $stars,
        ]);
    }

    /**
     * @param \WP_Post|false $post
     */
    protected function reviewFromRevision($post): Review
    {
        if (!is_a($post, \WP_Post::class)) {
            return new Review([], false);
        }
        if (wp_is_post_revision($post->ID)) {
            $meta = glsr(Database::class)->meta($post->ID, 'review');
            return new Review($meta);
        }
        return glsr(ReviewManager::class)->get($post->ID);
    }
}
