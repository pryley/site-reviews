<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Commands\CreateReview;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Defaults\CustomFieldsDefaults;
use GeminiLabs\SiteReviews\Defaults\RatingDefaults;
use GeminiLabs\SiteReviews\Defaults\UpdateReviewDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Sanitizer;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Review;
use GeminiLabs\SiteReviews\Reviews;

class ReviewManager
{
    public function assignPost(Review $review, int $postId): bool
    {
        if (!glsr()->can('assign_post', $postId)) {
            return false;
        }
        $where = [
            'is_published' => $this->isPublishedPost($postId),
            'post_id' => $postId,
            'rating_id' => $review->rating_id,
        ];
        if ($result = glsr(Database::class)->insert('assigned_posts', $where)) {
            glsr(Cache::class)->delete($review->ID, 'reviews');
            if (!defined('WP_IMPORTING')) {
                glsr(CountManager::class)->posts($postId);
            }
        }
        return Cast::toInt($result) > 0;
    }

    public function assignTerm(Review $review, int $termId): bool
    {
        $where = [
            'rating_id' => $review->rating_id,
            'term_id' => $termId,
        ];
        if ($result = glsr(Database::class)->insert('assigned_terms', $where)) {
            glsr(Cache::class)->delete($review->ID, 'reviews');
            if (!defined('WP_IMPORTING')) {
                glsr(CountManager::class)->terms($termId);
            }
        }
        return Cast::toInt($result) > 0;
    }

    public function assignUser(Review $review, int $userId): bool
    {
        $where = [
            'rating_id' => $review->rating_id,
            'user_id' => $userId,
        ];
        if ($result = glsr(Database::class)->insert('assigned_users', $where)) {
            glsr(Cache::class)->delete($review->ID, 'reviews');
            if (!defined('WP_IMPORTING')) {
                glsr(CountManager::class)->users($userId);
            }
        }
        return Cast::toInt($result) > 0;
    }

    /**
     * @return Review|false
     */
    public function create(CreateReview $command, ?int $postId = null)
    {
        if (empty($postId)) {
            $postId = $this->createRaw($command);
        }
        if (empty($postId)) {
            return false;
        }
        $review = $this->get($postId);
        if ($review->isValid()) {
            glsr()->action('review/created', $review, $command);
            return $this->get($review->ID); // return a fresh copy of the review
        }
        return false;
    }

    /**
     * @return Review|false
     */
    public function createFromPost(int $postId, array $data = [])
    {
        if (!Review::isReview($postId)) {
            return false;
        }
        $command = new CreateReview(new Request($data));
        glsr()->action('review/create', $postId, $command);
        return $this->create($command, $postId);
    }

    /**
     * @return int|false
     */
    public function createRaw(CreateReview $command)
    {
        $values = glsr()->args($command->toArray()); // this filters the values
        $submitted = $command->request->toArray();
        $metaInput = [
            '_submitted' => $submitted, // save the original submitted request in metadata
            '_submitted_hash' => md5(maybe_serialize($submitted)),
        ];
        $values = [
            'comment_status' => 'closed',
            'meta_input' => $metaInput,
            'ping_status' => 'closed',
            'post_author' => $values->author_id,
            'post_content' => $values->content,
            'post_date' => $values->date,
            'post_date_gmt' => $values->date_gmt,
            'post_modified' => $values->date,
            'post_modified_gmt' => $values->date_gmt,
            'post_name' => uniqid($values->type),
            'post_status' => $this->postStatus($command),
            'post_title' => $values->title,
            'post_type' => glsr()->post_type,
        ];
        $values = glsr()->filterArray('review/create/post_data', $values, $command);
        $postId = wp_insert_post($values, true);
        if (is_wp_error($postId)) {
            glsr_log()->error($postId->get_error_message())->debug($values);
            return false;
        }
        glsr()->action('review/create', $postId, $command);
        return $postId;
    }

    public function deleteRating(int $reviewId): bool
    {
        $result = glsr(Database::class)->delete('ratings', ['review_id' => $reviewId]);
        if ($result) {
            glsr(Cache::class)->delete($reviewId, 'reviews');
        }
        return Cast::toInt($result) > 0;
    }

    public function deleteRevisions(int $reviewId): void
    {
        $revisionIds = glsr(Query::class)->revisionIds((int) $reviewId);
        foreach ($revisionIds as $revisionId) {
            wp_delete_post_revision($revisionId);
        }
    }

    public function get(int $reviewId, bool $bypassCache = false): Review
    {
        return glsr(Query::class)->review($reviewId, $bypassCache);
    }

    public function reviews(array $args = []): Reviews
    {
        $args = (new NormalizePaginationArgs($args))->toArray();
        $results = glsr(Query::class)->reviews($args);
        $total = $this->total($args, $results);
        $reviews = new Reviews($results, $total, $args);
        glsr()->action('get/reviews', $reviews, $args);
        return $reviews;
    }

    public function total(array $args = [], array $reviews = []): int
    {
        return glsr(Query::class)->totalReviews($args, $reviews);
    }

    public function unassignPost(Review $review, int $postId): bool
    {
        if (!glsr()->can('unassign_post', $postId)) {
            return false;
        }
        $where = [
            'post_id' => $postId,
            'rating_id' => $review->rating_id,
        ];
        if ($result = glsr(Database::class)->delete('assigned_posts', $where)) {
            glsr(Cache::class)->delete($review->ID, 'reviews');
            glsr(CountManager::class)->posts($postId);
        }
        return Cast::toInt($result) > 0;
    }

    public function unassignTerm(Review $review, int $termId): bool
    {
        $where = [
            'rating_id' => $review->rating_id,
            'term_id' => $termId,
        ];
        if ($result = glsr(Database::class)->delete('assigned_terms', $where)) {
            glsr(Cache::class)->delete($review->ID, 'reviews');
            glsr(CountManager::class)->terms($termId);
        }
        return Cast::toInt($result) > 0;
    }

    public function unassignUser(Review $review, int $userId): bool
    {
        $where = [
            'rating_id' => $review->rating_id,
            'user_id' => $userId,
        ];
        if ($result = glsr(Database::class)->delete('assigned_users', $where)) {
            glsr(Cache::class)->delete($review->ID, 'reviews');
            glsr(CountManager::class)->users($userId);
        }
        return Cast::toInt($result) > 0;
    }

    /**
     * @return Review|false
     */
    public function update(int $reviewId, array $data = [])
    {
        $oldPost = get_post($reviewId);
        if (-1 === $this->updateRating($reviewId, $data)) {
            glsr_log('update rating failed');
            return false;
        }
        if (-1 === $this->updateReview($reviewId, $data)) {
            glsr_log('update review failed');
            return false;
        }
        $this->updateCustom($reviewId, $data);
        $this->updateResponse($reviewId, $data);
        $review = $this->get($reviewId);
        if ($assignedPosts = Arr::uniqueInt(Arr::get($data, 'assigned_posts'))) {
            glsr()->action('review/updated/post_ids', $review, $assignedPosts); // trigger a recount of assigned posts
        }
        if ($assignedUsers = Arr::uniqueInt(Arr::get($data, 'assigned_users'))) {
            glsr()->action('review/updated/user_ids', $review, $assignedUsers); // trigger a recount of assigned posts
        }
        $review = $this->get($reviewId); // get a fresh copy of the review
        glsr()->action('review/updated', $review, $data, $oldPost);
        return $review;
    }

    public function updateAssignedPost(int $postId): bool
    {
        $result = glsr(Database::class)->update('assigned_posts',
            ['is_published' => $this->isPublishedPost($postId)],
            ['post_id' => Cast::toInt($postId)]
        );
        return Cast::toInt($result) > 0;
    }

    public function updateCustom(int $reviewId, array $data = []): void
    {
        $data = glsr(CustomFieldsDefaults::class)->merge($data);
        $data = Arr::prefixKeys($data, 'custom_');
        foreach ($data as $metaKey => $metaValue) {
            glsr(Database::class)->metaSet($reviewId, $metaKey, $metaValue);
        }
    }

    public function updateRating(int $reviewId, array $data = []): int
    {
        $review = $this->get($reviewId);
        glsr(Cache::class)->delete($reviewId, 'reviews');
        $sanitized = glsr(RatingDefaults::class)->restrict($data);
        $data = array_intersect_key($sanitized, $data);
        if (empty($data)) {
            return 0;
        }
        $result = glsr(Database::class)->update('ratings', $data, [
            'review_id' => $reviewId,
        ]);
        if (false === $result) {
            return -1;
        }
        $rating = $data['rating'] ?? '';
        if (is_numeric($rating) && $review->rating !== $data['rating']) {
            glsr(CountManager::class)->recalculate();
        }
        return Cast::toInt($result);
    }

    public function updateResponse(int $reviewId, array $data): int
    {
        if (!array_key_exists('response', $data)) {
            return 0;
        }
        $response = Arr::get($data, 'response');
        $response = Cast::toString($response);
        $response = glsr(Sanitizer::class)->sanitizeTextHtml($response);
        $review = glsr_get_review($reviewId);
        if (empty($response) && empty($review->response)) {
            return 0;
        }
        glsr(Database::class)->metaSet($review->ID, 'response', $response); // prefixed metakey
        // This should run immediately after saving the response
        // but before adding the "response_by" meta_value!
        glsr()->action('review/responded', $review, $response);
        glsr(Database::class)->metaSet($review->ID, 'response_by', get_current_user_id()); // prefixed metakey
        glsr(Cache::class)->delete($review->ID, 'reviews');
        return 1;
    }

    public function updateReview(int $reviewId, array $data = []): int
    {
        if (glsr()->post_type !== get_post_type($reviewId)) {
            glsr_log()->error("Review update failed: Post ID [{$reviewId}] is not a review.");
            return -1;
        }
        glsr(Cache::class)->delete($reviewId, 'reviews');
        $sanitized = glsr(UpdateReviewDefaults::class)->restrict($data);
        if ($data = array_intersect_key($sanitized, $data)) {
            $data = array_filter([
                'post_content' => Arr::get($data, 'content'),
                'post_date' => Arr::get($data, 'date'),
                'post_date_gmt' => Arr::get($data, 'date_gmt'),
                'post_status' => Arr::get($data, 'status'),
                'post_title' => Arr::get($data, 'title'),
            ]);
        }
        if (empty($data)) {
            return 0;
        }
        $data = wp_parse_args(['ID' => $reviewId], $data);
        $result = wp_update_post($data, true);
        if (is_wp_error($result)) {
            glsr_log()->error($result->get_error_message())->debug($data);
            return -1;
        }
        return Cast::toInt($result);
    }

    protected function isPublishedPost(int $postId): bool
    {
        $isPublished = 'publish' === get_post_status($postId);
        return glsr()->filterBool('post/is-published', $isPublished, $postId);
    }

    protected function postStatus(CreateReview $command): string
    {
        $isApproved = $command->is_approved;
        $isFormSubmission = !defined('WP_IMPORTING') && !glsr()->retrieve('glsr_create_review', false);
        if ($isFormSubmission) {
            $requireApproval = glsr(OptionManager::class)->getBool('settings.general.require.approval');
            $requireApprovalForRating = glsr(OptionManager::class)->getInt('settings.general.require.approval_for', 5);
            $isApproved = !$requireApproval || $command->rating > $requireApprovalForRating;
        }
        return !$isApproved || ('local' === $command->type && $command->request->cast('blacklisted', 'bool'))
            ? 'pending'
            : 'publish';
    }
}
