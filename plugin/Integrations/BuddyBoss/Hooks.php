<?php

namespace GeminiLabs\SiteReviews\Integrations\BuddyBoss;

use GeminiLabs\SiteReviews\Hooks\AbstractHooks;

class Hooks extends AbstractHooks
{
    public function run(): void
    {
        if (!$this->isInstalled()) {
            return;
        }
        $this->hook(Controller::class, [
            ['filterProfileId', 'site-reviews/assigned_users/profile_id', 5],
        ]);
    }

    protected function isInstalled(): bool
    {
        return function_exists('bp_displayed_user_id');
    }
}
