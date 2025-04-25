<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Controllers\StatsController;

class StatsHooks extends AbstractHooks
{
    public function run(): void
    {
        $this->hook(StatsController::class, [
            ['filterReviewTemplateTags', 'site-reviews/review/build/after', 10, 3],
            ['geolocateReview', 'site-reviews/review/created', 10, 2],
        ]);
    }
}
