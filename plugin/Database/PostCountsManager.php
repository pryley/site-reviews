<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Modules\Rating;
use GeminiLabs\SiteReviews\Review;

class PostCountsManager
{
    /**
     * @var CountsManager
     */
    protected $manager;

    public function __construct()
    {
        $this->manager = glsr(CountsManager::class);
    }

    /**
     * @param int $postId
     * @return array
     */
    public function build($postId)
    {
        return $this->manager->buildCounts([
            'post_ids' => [$postId],
        ]);
    }

    /**
     * @return void
     */
    public function decrease(Review $review)
    {
        if (empty($counts = $this->get($review->assigned_to))) {
            return;
        }
        $this->update($review->assigned_to,
            $this->manager->decreaseRating($counts, $review->review_type, $review->rating)
        );
    }

    /**
     * @param int $postId
     * @return array
     */
    public function get($postId)
    {
        return array_filter((array) get_post_meta($postId, CountsManager::META_COUNT, true));
    }

    /**
     * @return void
     */
    public function increase(Review $review)
    {
        if (!(get_post($review->assigned_to) instanceof \WP_Post)) {
            return;
        }
        $counts = $this->get($review->assigned_to);
        $counts = empty($counts)
            ? $this->build($review->assigned_to)
            : $this->manager->increaseRating($counts, $review->review_type, $review->rating);
        $this->update($review->assigned_to, $counts);
    }

    /**
     * @param int $postId
     * @return void
     */
    public function update($postId, array $reviewCounts)
    {
        $ratingCounts = $this->manager->flatten($reviewCounts);
        update_post_meta($postId, CountsManager::META_COUNT, $reviewCounts);
        update_post_meta($postId, CountsManager::META_AVERAGE, glsr(Rating::class)->getAverage($ratingCounts));
        update_post_meta($postId, CountsManager::META_RANKING, glsr(Rating::class)->getRanking($ratingCounts));
    }

    /**
     * @return void
     */
    public function updateAll()
    {
        glsr(SqlQueries::class)->deletePostCountMetaKeys();
        $postIds = glsr(SqlQueries::class)->getReviewsMeta('assigned_to');
        foreach ($postIds as $postId) {
            $this->update($postId, $this->build($postId));
        }
    }
}
