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
use GeminiLabs\SiteReviews\Database\Cache;
use GeminiLabs\SiteReviews\Database\CountManager;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Defaults\RatingDefaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Metaboxes\ResponseMetabox;
use GeminiLabs\SiteReviews\Modules\Avatar;
use GeminiLabs\SiteReviews\Modules\Html\ReviewHtml;
use GeminiLabs\SiteReviews\Modules\Queue;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Review;

class ReviewController extends AbstractController
{
    /**
     * @param \WP_Post[] $posts
     *
     * @return \WP_Post[]
     *
     * @filter the_posts
     */
    public function filterPostsToCacheReviews(array $posts): array
    {
        $reviews = array_filter($posts, fn ($post) => glsr()->post_type === $post->post_type);
        if ($postIds = wp_list_pluck($reviews, 'ID')) {
            glsr(Query::class)->reviews([], $postIds); // this caches the associated Review objects
        }
        return $posts;
    }

    /**
     * @filter wp_insert_post_data
     */
    public function filterReviewPostData(array $data, array $sanitized): array
    {
        if (empty($sanitized['ID']) || empty($sanitized['action']) || glsr()->post_type !== Arr::get($sanitized, 'post_type')) {
            return $data;
        }
        if (!empty(filter_input(INPUT_GET, 'bulk_edit'))) {
            if (is_numeric(filter_input(INPUT_GET, 'post_author'))) {
                $data['post_author'] = filter_input(INPUT_GET, 'post_author');
            } else {
                unset($data['post_author']);
            }
        }
        if (is_numeric(filter_input(INPUT_POST, 'post_author_override'))) {
            // use the value from the author meta box
            $data['post_author'] = filter_input(INPUT_POST, 'post_author_override');
        }
        return $data;
    }

    /**
     * @filter site-reviews/rendered/template/review
     */
    public function filterReviewTemplate(string $template, array $data): string
    {
        $search = 'id="review-';
        $dataType = Arr::get($data, 'review.type', 'local');
        $replace = sprintf('data-type="%s" %s', $dataType, $search);
        if (Arr::get($data, 'review.is_pinned')) {
            $replace = 'data-pinned="1" '.$replace;
        }
        if (Arr::get($data, 'review.is_verified')) {
            $replace = 'data-verified="1" '.$replace;
        }
        return str_replace($search, $replace, $template);
    }

    /**
     * @filter site-reviews/query/sql/clause/operator
     */
    public function filterSqlClauseOperator(string $operator): string
    {
        $operators = ['loose' => 'OR', 'strict' => 'AND'];
        return Arr::get($operators, glsr_get_option('reviews.assignment', 'strict', 'string'), $operator);
    }

    /**
     * @filter site-reviews/review/build/after
     */
    public function filterTemplateTags(array $tags, Review $review, ReviewHtml $reviewHtml): array
    {
        $tags['assigned_links'] = $reviewHtml->buildTemplateTag($review, 'assigned_links', $review->assigned_posts);
        return $tags;
    }

    /**
     * Triggered after one or more categories are added or removed from a review.
     *
     * @action set_object_terms
     */
    public function onAfterChangeAssignedTerms(
        int $postId,
        array $terms,
        array $newTTIds,
        string $taxonomy,
        bool $append,
        array $oldTTIds
    ): void {
        if (Review::isReview($postId)) {
            $review = glsr(ReviewManager::class)->get($postId);
            $diff = $this->getAssignedDiffs($oldTTIds, $newTTIds);
            $this->execute(new UnassignTerms($review, $diff['old']));
            $this->execute(new AssignTerms($review, $diff['new']));
        }
    }

    /**
     * Triggered when a post status changes or when a review is approved|unapproved|trashed.
     *
     * @action transition_post_status
     */
    public function onAfterChangeStatus(string $new, string $old, ?\WP_Post $post): void
    {
        if (is_null($post)) {
            return; // This should never happen, but some plugins are bad actors so...
        }
        if (in_array($old, ['new', $new])) {
            return;
        }
        if (Review::isReview($post)) {
            $isAutoDraft = 'auto-draft' === $old && 'auto-draft' !== $new;
            if ($isAutoDraft) {
                glsr(ReviewManager::class)->createFromPost($post->ID);
            }
            $isPublished = 'publish' === $new;
            glsr(ReviewManager::class)->updateRating($post->ID, ['is_approved' => $isPublished]);
            glsr(Cache::class)->delete($post->ID, 'reviews');
            glsr(CountManager::class)->recalculate();
            if ($isAutoDraft) {
                return;
            }
            $review = glsr_get_review($post->ID);
            if ('publish' === $new) {
                glsr()->action('review/approved', $review, $old, $new);
            } elseif ('pending' === $new) {
                glsr()->action('review/unapproved', $review, $old, $new);
            } elseif ('trash' === $new) {
                glsr()->action('review/trashed', $review, $old, $new);
            }
            glsr()->action('review/transitioned', $review, $new, $old);
        } else {
            glsr(ReviewManager::class)->updateAssignedPost($post->ID);
        }
    }

    /**
     * Fallback action if ajax is not working for any reason.
     *
     * @action admin_action_approve
     */
    public function onApprove(): void
    {
        if (glsr()->id === filter_input(INPUT_GET, 'plugin')) {
            check_admin_referer('approve-review_'.($postId = $this->getPostId()));
            $this->execute(new ToggleStatus(new Request([
                'post_id' => $postId,
                'status' => 'publish',
            ])));
            wp_safe_redirect(wp_get_referer());
            exit;
        }
    }

    /**
     * Triggered when a review's assigned post IDs are updated.
     *
     * @action site-reviews/review/updated/post_ids
     */
    public function onChangeAssignedPosts(Review $review, array $postIds = []): void
    {
        $diff = $this->getAssignedDiffs($review->assigned_posts, $postIds);
        $this->execute(new UnassignPosts($review, $diff['old']));
        $this->execute(new AssignPosts($review, $diff['new']));
    }

    /**
     * Triggered when a review's assigned users IDs are updated.
     *
     * @action site-reviews/review/updated/user_ids
     */
    public function onChangeAssignedUsers(Review $review, array $userIds = []): void
    {
        $diff = $this->getAssignedDiffs($review->assigned_users, $userIds);
        $this->execute(new UnassignUsers($review, $diff['old']));
        $this->execute(new AssignUsers($review, $diff['new']));
    }

    /**
     * Triggered after a review is created.
     *
     * @action site-reviews/review/created
     */
    public function onCreatedReview(Review $review, CreateReview $command): void
    {
        $this->execute(new AssignPosts($review, $command->assigned_posts));
        $this->execute(new AssignUsers($review, $command->assigned_users));
    }

    /**
     * Triggered when a review is created.
     *
     * @action site-reviews/review/create
     */
    public function onCreateReview(int $postId, CreateReview $command): void
    {
        $values = glsr()->args($command->toArray()); // this filters the values
        $data = glsr(RatingDefaults::class)->restrict($values->toArray());
        $data['review_id'] = $postId;
        $data['is_approved'] = 'publish' === get_post_status($postId);
        if (false === glsr(Database::class)->insert('ratings', $data)) {
            glsr_log()->error('A review could not be created. Here are some things to try which may fix the problem:'.
                PHP_EOL.'1. First, deactivate Site Reviews and then reactivate it (this should fix any broken database table indexes).'.
                PHP_EOL.'2. Next, hold down the ALT key (Option key if using a Mac) and run the Migrate Plugin tool.'.
                PHP_EOL.'3. Finally, run the "Repair Review Relations" tool.'.
                PHP_EOL.'4. If the problem persists, please use the "Contact Support" section on the Help page.'
            );
            glsr_log()->debug($data);
            wp_delete_post($postId, true); // remove post as review was not created
            return;
        }
        $termIds = wp_set_object_terms($postId, $values->assigned_terms, glsr()->taxonomy);
        if (is_wp_error($termIds)) {
            glsr_log()->error($termIds->get_error_message());
        }
        if ($excluded = Cast::toArray($command->request()->decrypt('excluded'))) {
            glsr(Database::class)->metaSet($postId, 'excluded', $excluded); // save the fields hidden in the review form
        }
        if (!empty($values->response)) { // save the response if one is provided
            glsr(Database::class)->metaSet($postId, 'response', $values->response);
            glsr(Database::class)->metaSet($postId, 'response_by', $values->response_by); // @phpstan-ignore-line
        }
        foreach ($values->custom as $key => $value) {
            glsr(Database::class)->metaSet($postId, "custom_{$key}", $value);
        }
    }

    /**
     * Triggered when a review or other post type is deleted and the posts table uses the MyISAM engine.
     *
     * @action deleted_post
     */
    public function onDeletePost(int $postId, \WP_Post $post): void
    {
        if (glsr()->post_type === $post->post_type) {
            $this->onDeleteReview($postId);
            return;
        }
        $reviewIds = glsr(Query::class)->reviewIds([
            'assigned_posts' => $postId,
            'per_page' => -1,
            'status' => 'all',
        ]);
        if (glsr(Database::class)->delete('assigned_posts', ['post_id' => $postId])) {
            array_walk($reviewIds, function ($reviewId) {
                glsr(Cache::class)->delete($reviewId, 'reviews');
            });
        }
    }

    /**
     * Triggered when a review is deleted and the posts table uses the MyISAM engine.
     *
     * @see $this->onDeletePost()
     */
    public function onDeleteReview(int $reviewId): void
    {
        glsr(ReviewManager::class)->deleteRating($reviewId);
    }

    /**
     * Triggered when a user is deleted and the users table uses the MyISAM engine.
     *
     * @action deleted_user
     */
    public function onDeleteUser(int $userId = 0): void
    {
        $reviewIds = glsr(Query::class)->reviewIds([
            'assigned_users' => $userId,
            'per_page' => -1,
            'status' => 'all',
        ]);
        if (glsr(Database::class)->delete('assigned_users', ['user_id' => $userId])) {
            array_walk($reviewIds, function ($reviewId) {
                glsr(Cache::class)->delete($reviewId, 'reviews');
            });
        }
    }

    /**
     * Triggered when a review is edited or trashed.
     * It's unnecessary to trigger a term recount as this is done by the set_object_terms hook
     * We need to use "post_updated" to support revisions (vs "save_post").
     *
     * @action post_updated
     */
    public function onEditReview(int $postId, ?\WP_Post $post, ?\WP_Post $oldPost): void
    {
        if (is_null($post) || is_null($oldPost)) {
            return; // This should never happen, but some plugins are bad actors so...
        }
        if (!glsr()->can('edit_posts') || !$this->isEditedReview($post, $oldPost)) {
            return;
        }
        if (glsr()->id === filter_input(INPUT_GET, 'plugin')) {
            return; // the fallback approve/unapprove action is being run
        }
        if (!in_array(glsr_current_screen()->base, ['edit', 'post'])) {
            return; // only trigger this action from the Site Reviews edit/post screens
        }
        $review = glsr(ReviewManager::class)->get($postId);
        if ('edit' === glsr_current_screen()->base) {
            $this->bulkUpdateReview($review, $oldPost);
        } else {
            $this->updateReview($review, $oldPost);
        }
    }

    /**
     * Fallback action if ajax is not working for any reason.
     *
     * @action admin_action_unapprove
     */
    public function onUnapprove(): void
    {
        if (glsr()->id === filter_input(INPUT_GET, 'plugin')) {
            $postId = $this->getPostId();
            check_admin_referer("unapprove-review_{$postId}");
            $this->execute(new ToggleStatus(new Request([
                'post_id' => $postId,
                'status' => 'publish',
            ])));
            wp_safe_redirect(wp_get_referer());
            exit;
        }
    }

    /**
     * @action site-reviews/review/created
     */
    public function sendNotification(Review $review): void
    {
        if (defined('WP_IMPORTING')) {
            return;
        }
        if (empty(glsr_get_option('general.notifications'))) {
            return;
        }
        if (!in_array($review->status, ['pending', 'publish'])) {
            return; // this review is likely a draft made in the wp-admin
        }
        glsr(Queue::class)->async('queue/notification', ['review_id' => $review->ID]);
    }

    protected function bulkUpdateReview(Review $review, \WP_Post $oldPost): void
    {
        if ($assignedPostIds = filter_input(INPUT_GET, 'post_ids', FILTER_SANITIZE_NUMBER_INT, FILTER_FORCE_ARRAY)) {
            glsr()->action('review/updated/post_ids', $review, Cast::toArray($assignedPostIds)); // trigger a recount of assigned posts
        }
        if ($assignedUserIds = filter_input(INPUT_GET, 'user_ids', FILTER_SANITIZE_NUMBER_INT, FILTER_FORCE_ARRAY)) {
            glsr()->action('review/updated/user_ids', $review, Cast::toArray($assignedUserIds)); // trigger a recount of assigned users
        }
        $review = glsr(ReviewManager::class)->get($review->ID); // get a fresh copy of the review
        glsr()->action('review/updated', $review, [], $oldPost); // pass an empty array since review values are unchanged
    }

    protected function getAssignedDiffs(array $existing, array $replacements): array
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

    protected function isEditedReview(\WP_Post $post, \WP_Post $oldPost): bool
    {
        if (glsr()->post_type !== $post->post_type) {
            return false;
        }
        if (in_array('trash', [$post->post_status, $oldPost->post_status])) {
            return false; // trashed posts cannot be edited
        }
        $input = 'edit' === glsr_current_screen()->base ? INPUT_GET : INPUT_POST;
        return filter_input($input, 'action') !== glsr()->prefix.'admin_action'; // abort if not a proper post update (i.e. approve/unapprove)
    }

    protected function refreshAvatar(array $data, Review $review): string
    {
        $avatarUrl = Cast::toString($data['avatar'] ?? '');
        if ($review->author === ($data['name'] ?? false)) {
            return $avatarUrl;
        }
        $url = preg_replace('/(.*)\/site-reviews\/avatars\/[\p{L&}]+\.svg$/u', '', $avatarUrl);
        if (empty($url)) { // only update the initials fallback avatar
            $review->set('author', $data['name'] ?? '');
            $avatarUrl = glsr(Avatar::class)->generateInitials($review);
        }
        return $avatarUrl;
    }

    protected function updateReview(Review $review, \WP_Post $oldPost): void
    {
        $customDefaults = array_fill_keys(array_keys($review->custom()->toArray()), '');
        $data = Helper::filterInputArray(glsr()->id);
        $data = wp_parse_args($data, $customDefaults); // this ensures we save all empty custom values
        if (Arr::get($data, 'is_editing_review')) {
            $data['avatar'] = $this->refreshAvatar($data, $review);
            $data['rating'] = Arr::get($data, 'rating');
            $data['terms'] = Arr::get($data, 'terms', 0);
            if (!glsr()->filterBool('verification/enabled', false)) {
                unset($data['is_verified']);
            }
            glsr(ReviewManager::class)->updateRating($review->ID, $data); // values are sanitized here
            glsr(ReviewManager::class)->updateCustom($review->ID, $data); // values are sanitized here
            $review = glsr(ReviewManager::class)->get($review->ID); // get a fresh copy of the review
        }
        $assignedPostIds = filter_input(INPUT_POST, 'post_ids', FILTER_SANITIZE_NUMBER_INT, FILTER_FORCE_ARRAY);
        $assignedUserIds = filter_input(INPUT_POST, 'user_ids', FILTER_SANITIZE_NUMBER_INT, FILTER_FORCE_ARRAY);
        glsr()->action('review/updated/post_ids', $review, Cast::toArray($assignedPostIds)); // trigger a recount of assigned posts
        glsr()->action('review/updated/user_ids', $review, Cast::toArray($assignedUserIds)); // trigger a recount of assigned users
        glsr(ResponseMetabox::class)->save($review);
        $review = glsr(ReviewManager::class)->get($review->ID); // get a fresh copy of the review
        glsr()->action('review/updated', $review, $data, $oldPost);
    }
}
