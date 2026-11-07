<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Controllers\VerificationController;

class VerificationHooks extends AbstractHooks
{
    public function run(): void
    {
        $this->hook(VerificationController::class, [
            ['renderVerifyAction', 'post_submitbox_misc_actions', 20],
            ['sendVerificationEmail', 'site-reviews/review/created', 10, 2],
            ['verifyReview', 'site-reviews/route/get/public/verify'],
        ]);
    }
}
