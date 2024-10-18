<?php

namespace GeminiLabs\SiteReviews\Integrations\UltimateMember;

use GeminiLabs\SiteReviews\Hooks\AbstractHooks;

class Hooks extends AbstractHooks
{
    public function run(): void
    {
        if (!$this->isInstalled()) {
            return;
        }
        $this->hook(Controller::class, [
            ['filterAvatarUrl', 'site-reviews/avatar/generate', 10, 2],
            ['filterProfileId', 'site-reviews/assigned_users/profile_id', 5],
        ]);
    }

    protected function isInstalled(): bool
    {
        return function_exists('um_get_default_avatar_uri')
            && function_exists('um_get_requested_user');
    }
}
