<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Database\RatingManager;

class AssignUsers implements Contract
{
    public $ratingId;
    public $userIds;

    public function __construct($ratingId, array $userIds)
    {
        $this->ratingId = $ratingId;
        $this->userIds = $userIds;
    }

    /**
     * @return void
     */
    public function handle()
    {
        foreach ($this->userIds as $userId) {
            glsr(RatingManager::class)->assignUser($this->ratingId, $userId);
        }
    }
}
