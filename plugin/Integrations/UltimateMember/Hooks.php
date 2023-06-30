<?php

namespace GeminiLabs\SiteReviews\Integrations\UltimateMember;

use GeminiLabs\SiteReviews\Hooks\AbstractHooks;

class Hooks extends AbstractHooks
{
    public function run(): void
    {
        if (!function_exists('um_get_default_avatar_uri')) {
            return;
        }
        $this->hook(Controller::class, [
            ['filterAvatarUrl', 'site-reviews/avatar/generate', 10, 2],
        ]);
    }
}
