<?php

namespace GeminiLabs\SiteReviews\Integrations\GamiPress;

use GeminiLabs\SiteReviews\Hooks\AbstractHooks;

class Hooks extends AbstractHooks
{
    public function run(): void
    {
        if (glsr()->addon('site-reviews-gamipress') || !defined('GAMIPRESS_VER')) {
            return;
        }
        $this->hook(Controller::class, [
            ['filterActivityTriggerLabel', 'gamipress_activity_trigger_label', 10, 3],
            ['filterActivityTriggers', 'gamipress_activity_triggers'],
            ['filterUserHasAccessToAchievement', 'user_has_access_to_achievement', 10, 4],
            ['onReviewCreated', 'site-reviews/review/created', 20],
        ]);
    }
}
