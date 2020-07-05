<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Commands\AssignPosts;
use GeminiLabs\SiteReviews\Commands\AssignTerms;
use GeminiLabs\SiteReviews\Commands\AssignUsers;
use GeminiLabs\SiteReviews\Commands\CreateReview;
use GeminiLabs\SiteReviews\Commands\ToggleStatus;
use GeminiLabs\SiteReviews\Commands\UnassignPosts;
use GeminiLabs\SiteReviews\Commands\UnassignTerms;
use GeminiLabs\SiteReviews\Commands\UnassignUsers;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Database\TaxonomyManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Review;
use WP_Post;

class ReviewController extends Controller
{
    /**
     * @return void
     * @action admin_action_approve
     */
    public function approve()
    {
        if (glsr()->id == filter_input(INPUT_GET, 'plugin')) {
            check_admin_referer('approve-review_'.($postId = $this->getPostId()));
            $this->execute(new ToggleStatus($postId, 'publish'));
            wp_safe_redirect(wp_get_referer());
            exit;
        }
    }

    /**
     * @param array $posts
     * @return array
     * @filter the_posts
     */
    public function filterPostsToCacheReviews($posts)
    {
        $reviews = array_filter($posts, function ($post) {
            return glsr()->post_type === $post->post_type;
        });
        if ($postIds = wp_list_pluck($reviews, 'ID')) {
            glsr(Query::class)->reviews([], $postIds); // this caches the associated Review objects
        }
        return $posts;
    }

    /**
     * Triggered when one or more categories are added or removed from a review.
     *
     * @param int $postId
     * @param array $terms
     * @param array $newTTIds
     * @param string $taxonomy
     * @param bool $append
     * @param array $oldTTIds
     * @return void
     * @action set_object_terms
     */
    public function onAfterChangeAssignedTerms($postId, $terms, $newTTIds, $taxonomy, $append, $oldTTIds)
    {
        if (Review::isReview($postId)) {
            $review = glsr(Query::class)->review($postId);
            $diff = $this->getAssignedDiffs($oldTTIds, $newTTIds);
            $this->execute(new UnassignTerms($review, $diff['old']));
            $this->execute(new AssignTerms($review, $diff['new']));
        }
    }

    /**
     * Triggered when a post status changes or when a review is approved|unapproved|trashed.
     *
     * @param string $oldStatus
     * @param string $newStatus
     * @param \WP_Post $post
     * @return void
     * @action transition_post_status
     */
    public function onAfterChangeStatus($newStatus, $oldStatus, $post)
    {
        if (in_array($oldStatus, ['new', $newStatus])) {
            return;
        }
        $isPublished = 'publish' === $newStatus;
        if (Review::isReview($post)) {
            glsr(ReviewManager::class)->update($post->ID, ['is_approved' => $isPublished]);
        } else {
            glsr(ReviewManager::class)->updateAssignedPost($post->ID, $isPublished);
        }
    }

    /**
     * Triggered when a review's assigned post IDs are updated.
     *
     * @return void
     * @action site-reviews/review/updated/post_ids
     */
    public function onChangeAssignedPosts(Review $review, array $postIds = [])
    {
        $diff = $this->getAssignedDiffs($review->assigned_post_ids, $postIds);
        $this->execute(new UnassignPosts($review, $diff['old']));
        $this->execute(new AssignPosts($review, $diff['new']));
    }

    /**
     * Triggered when a review's assigned users IDs are updated.
     *
     * @return void
     * @action site-reviews/review/updated/user_ids
     */
    public function onChangeAssignedUsers(Review $review, array $userIds = [])
    {
        $diff = $this->getAssignedDiffs($review->assigned_user_ids, $userIds);
        $this->execute(new UnassignUsers($review, $diff['old']));
        $this->execute(new AssignUsers($review, $diff['new']));
    }

    /**
     * Triggered when a review is created.
     *
     * @param int $postId
     * @return void
     * @action site-reviews/review/create
     */
    public function onCreateReview($postId, CreateReview $command)
    {
        if (glsr(Database::class)->insert($postId, (array) $command)) {
            glsr(TaxonomyManager::class)->setTerms($postId, $command->assigned_term_ids);
            return;
        }
        glsr_log()->error('[INSERT] DB error thrown when creating review.');
        wp_delete_post($postId, true);
    }

    /**
     * Triggered when a review is edited.
     * We need to use "edit_post" to support revisions (vs "save_post")
     *
     * @param int $postId
     * @return void
     * @action edit_post_{glsr()->post_type}
     */
    public function onEditReview($postId)
    {
        if (!glsr()->can('edit_posts')) {
            return;
        }
        $review = glsr(Query::class)->review($postId);
        $input = 'edit' === glsr_current_screen()->base ? INPUT_GET : INPUT_POST;
        $assignedPostIds = filter_input($input, 'post_ids', FILTER_SANITIZE_NUMBER_INT, FILTER_FORCE_ARRAY);
        $assignedUserIds = filter_input($input, 'user_ids', FILTER_SANITIZE_NUMBER_INT, FILTER_FORCE_ARRAY);
        glsr()->action('review/updated/post_ids', $review, Arr::consolidate($assignedPostIds));
        glsr()->action('review/updated/user_ids', $review, Arr::consolidate($assignedUserIds));
        glsr(MetaboxController::class)->saveResponseMetabox($postId);
        $reviewFields = Helper::filterInputArray(glsr()->id);
        if (Arr::get($reviewFields, 'is_editing_review')) {
            $reviewFields['rating'] = Arr::get($reviewFields, 'rating', 0);
            glsr(ReviewManager::class)->update($postId, $reviewFields);
        }
        glsr()->action('review/saved', glsr(Query::class)->review($postId));
    }

    /**
     * @return void
     * @action admin_action_unapprove
     */
    public function unapprove()
    {
        if (glsr()->id == filter_input(INPUT_GET, 'plugin')) {
            check_admin_referer('unapprove-review_'.($postId = $this->getPostId()));
            $this->execute(new ToggleStatus($postId, 'pending'));
            wp_safe_redirect(wp_get_referer());
            exit;
        }
    }

    /**
     * @return array
     */
    protected function getAssignedDiffs(array $existing, array $replacements)
    {
        sort($existing);
        sort($replacements);
        $new = $old = [];
        if ($existing !== $replacements) {
            $ignored = array_intersect($existing, $replacements);
            $new = array_diff($replacements, $ignored);
            $old = array_diff($existing, $ignored);
        }
        return [
            'new' => $new,
            'old' => $old,
        ];
    }
}
