<?php

namespace GeminiLabs\SiteReviews\Integrations\Cache;

use GeminiLabs\SiteReviews\Integrations\IntegrationHooks;

class Hooks extends IntegrationHooks
{
    public function run(): void
    {
        $this->hook(Controller::class, [
            // ['flush', 'site-reviews/review/geolocated', 50, 2],
            // ['flush', 'site-reviews/review/pinned', 50, 2],
            // ['flush', 'site-reviews/review/responded', 50, 2],
            // ['flush', 'site-reviews/review/verified', 50],
            ['flushAfterCreated', 'site-reviews/review/created', 50, 2],
            ['flushAfterMigrated', 'site-reviews/migration/end', 50],
            ['flushAfterTransitioned', 'site-reviews/review/transitioned', 50, 3],
            ['flushAfterUpdated', 'site-reviews/review/updated', 50],
            ['flushReviewCache', 'site-reviews/cache/flush'],
        ]);
    }
}
