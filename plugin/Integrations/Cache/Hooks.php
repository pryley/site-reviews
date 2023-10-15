<?php

namespace GeminiLabs\SiteReviews\Integrations\Cache;

use GeminiLabs\SiteReviews\Hooks\AbstractHooks;

class Hooks extends AbstractHooks
{
    public function run(): void
    {
        $this->hook(Controller::class, [
            ['purgeAll', 'site-reviews/migration/end'],
            ['purgeOnCreated', 'site-reviews/review/created', 10, 2],
            ['purgeOnUpdated', 'site-reviews/review/approved'],
            ['purgeOnUpdated', 'site-reviews/review/unapproved'],
            ['purgeOnUpdated', 'site-reviews/review/updated'],
            ['purge', 'shutdown'],
        ]);
    }
}
