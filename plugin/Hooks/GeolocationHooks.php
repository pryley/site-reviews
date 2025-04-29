<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Controllers\GeolocationController;

class GeolocationHooks extends AbstractHooks
{
    public function run(): void
    {
        $this->hook(GeolocationController::class, [
            ['filterReviewTemplateTags', 'site-reviews/review/build/after', 10, 3],
            ['geolocateReview', 'site-reviews/review/created', 10, 2],
        ]);
    }
}
