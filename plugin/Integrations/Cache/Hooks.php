<?php

namespace GeminiLabs\SiteReviews\Integrations\Cache;

use GeminiLabs\SiteReviews\Hooks\AbstractHooks;

class Hooks extends AbstractHooks
{
    public function run(): void
    {
        $this->hook(Controller::class, [
            ['purgeAll', 'site-reviews/migration/end'],
            ['purgeForPost', 'site-reviews/review/created', 10, 2],
        ]);
    }
}
