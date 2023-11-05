<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Review;

class UnassignPosts extends AbstractCommand
{
    /** @var Review */
    public $review;
    public array $postIds = [];

    public function __construct(Review $review, array $postIds)
    {
        $this->review = $review;
        $this->postIds = Arr::uniqueInt($postIds);
    }

    public function handle(): void
    {
        foreach ($this->postIds as $postId) {
            glsr(ReviewManager::class)->unassignPost($this->review, $postId);
        }
    }
}
