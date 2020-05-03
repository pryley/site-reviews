<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Database\RatingManager;

class AssignTerms implements Contract
{
    public $ratingId;
    public $termIds;

    public function __construct($ratingId, array $termIds)
    {
        $this->ratingId = $ratingId;
        $this->termIds = $termIds;
    }

    /**
     * @return void
     */
    public function handle()
    {
        foreach ($this->termIds as $termId) {
            glsr(RatingManager::class)->assignTerm($this->ratingId, $termId);
        }
    }
}
