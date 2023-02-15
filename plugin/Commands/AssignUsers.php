<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Review;

class AssignUsers implements Contract
{
    public $review;
    public $userIds;

    public function __construct(Review $review, array $userIds)
    {
        $this->review = $review;
        $this->userIds = Arr::uniqueInt($userIds);
    }

    /**
     * @return void
     */
    public function handle()
    {
        foreach ($this->userIds as $userId) {
            glsr(ReviewManager::class)->assignUser($this->review, $userId);
        }
    }
}
