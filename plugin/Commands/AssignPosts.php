<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Review;

class AssignPosts extends AbstractCommand
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
            glsr(ReviewManager::class)->assignPost($this->review, $postId);
        }
    }
}
