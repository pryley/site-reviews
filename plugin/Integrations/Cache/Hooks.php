<?php

namespace GeminiLabs\SiteReviews\Integrations\Cache;

use GeminiLabs\SiteReviews\Hooks\AbstractHooks;

class Hooks extends AbstractHooks
{
    public function run(): void
    {
        $this->hook(Controller::class, [
            ['flushAfterCreated', 'site-reviews/review/created', 10, 2],
            ['flushAfterMigrated', 'site-reviews/migration/end'],
            ['flushBeforeDeleted', 'delete_post', 10, 2],
            ['flushBeforeTrashed', 'wp_trash_post'],
            ['flushPostCache', 'clean_post_cache', 10, 2],
            ['flushReviewCache', 'site-reviews/cache/flush'],
        ]);
    }
}
