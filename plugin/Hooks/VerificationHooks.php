<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Controllers\VerificationController;

class VerificationHooks extends AbstractHooks
{
    public function run(): void
    {
        $this->hook(VerificationController::class, [
            ['sendVerificationEmail', 'site-reviews/review/created', 10, 2],
            ['toggleVerifiedAjax', 'site-reviews/route/ajax/toggle-verified'],
            ['verifiedReviewAjax', 'site-reviews/route/ajax/verified-review'],
            ['verifyReview', 'site-reviews/route/get/public/verify'],
        ]);
    }
}
