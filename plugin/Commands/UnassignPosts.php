<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Review;

class UnassignPosts implements Contract
{
    public $review;
    public $postIds;

    public function __construct(Review $review, array $postIds)
    {
        $this->review = $review;
        $this->postIds = Arr::uniqueInt($postIds);
    }

    /**
     * @return void
     */
    public function handle()
    {
        foreach ($this->postIds as $postId) {
            glsr(ReviewManager::class)->unassignPost($this->review, $postId);
        }
    }
}
