<?php

namespace GeminiLabs\SiteReviews\Integrations\Cache;

use GeminiLabs\SiteReviews\Hooks\AbstractHooks;

class Hooks extends AbstractHooks
{
    public function run(): void
    {
        $this->hook(Controller::class, [
            ['initPurge', 'admin_footer'],
            ['initPurge', 'wp_footer'],
            ['purge', 'site-reviews/cache/flush'],
            ['purgeAll', 'site-reviews/migration/end'],
            ['purgeOnCreated', 'site-reviews/review/created', 10, 2],
            ['purgeOnTransitioned', 'site-reviews/review/transitioned', 10, 3],
            ['purgeOnUpdated', 'site-reviews/review/unapproved'],
        ]);
    }
}
