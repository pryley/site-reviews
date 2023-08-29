<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Controllers\UserController;

class UserHooks extends AbstractHooks
{
    public function run(): void
    {
        $this->hook(UserController::class, [
            ['filterMapMetaCap', 'map_meta_cap', 10, 4],
            ['filterUserHasCap', 'user_has_cap', 10, 3],
        ]);
    }
}
