<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Review;

/**
 * A review saved from the Reviews list table's quick or bulk edit.
 */
class BulkUpdateReview extends AbstractCommand
{
    protected \WP_Post $oldPost;
    protected Review $review;

    public function __construct(Review $review, \WP_Post $oldPost)
    {
        $this->oldPost = $oldPost;
        $this->review = $review;
    }

    public function handle(): void
    {
        if ($assignedPostIds = filter_input(\INPUT_GET, 'post_ids', \FILTER_SANITIZE_NUMBER_INT, \FILTER_FORCE_ARRAY)) {
            glsr()->action('review/updated/post_ids', $this->review, Cast::toArray($assignedPostIds)); // trigger a recount of assigned posts
        }
        if ($assignedUserIds = filter_input(\INPUT_GET, 'user_ids', \FILTER_SANITIZE_NUMBER_INT, \FILTER_FORCE_ARRAY)) {
            glsr()->action('review/updated/user_ids', $this->review, Cast::toArray($assignedUserIds)); // trigger a recount of assigned users
        }
        $this->review->refresh();
        glsr()->action('review/updated', $this->review, [], $this->oldPost); // pass an empty array since review values are unchanged
    }
}
