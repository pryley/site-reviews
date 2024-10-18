<?php

namespace GeminiLabs\SiteReviews\Integrations\BuddyBoss;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Review;

class Controller extends AbstractController
{
    /**
     * @filter site-reviews/assigned_users/profile_id
     */
    public function filterProfileId(int $profileId): int
    {
        if (empty($profileId)) {
            return (int) bp_displayed_user_id();
        }
        return $profileId;
    }
}
