<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Review;

class AssignUsers extends AbstractCommand
{
    /** @var Review */
    public $review;
    public array $userIds = [];

    public function __construct(Review $review, array $userIds)
    {
        $this->review = $review;
        $this->userIds = Arr::uniqueInt($userIds);
    }

    public function handle(): void
    {
        foreach ($this->userIds as $userId) {
            glsr(ReviewManager::class)->assignUser($this->review, $userId);
        }
    }
}
