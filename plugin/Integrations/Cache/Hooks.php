<?php

namespace GeminiLabs\SiteReviews\Integrations\Cache;

use GeminiLabs\SiteReviews\Integrations\IntegrationHooks;

class Hooks extends IntegrationHooks
{
    public function run(): void
    {
        $this->hook(Controller::class, [
            ['flush', 'site-reviews/cache/flush', 10, 2],
            ['flushAfterCreated', 'site-reviews/review/created', 50, 2],
            ['flushAfterMigrated', 'site-reviews/migration/end', 50],
            ['flushAfterTransitioned', 'site-reviews/review/transitioned', 50, 3],
            ['flushAfterUpdated', 'site-reviews/review/updated', 50],
            ['flushAll', 'site-reviews/cache/flush_all'],
        ]);
    }
}
