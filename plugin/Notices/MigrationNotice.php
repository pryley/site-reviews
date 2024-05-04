<?php

namespace GeminiLabs\SiteReviews\Notices;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Modules\Migrate;
use GeminiLabs\SiteReviews\Modules\Queue;

class MigrationNotice extends AbstractNotice
{
    protected function canRender(): bool
    {
        if (!parent::canRender()) {
            return false;
        }
        if (glsr(Queue::class)->isPending('queue/migration')) {
            return true;
        }
        $args = []; // informational only
        if (glsr(Database::class)->isMigrationNeeded()) {
            $args['database'] = true;
        }
        if (glsr(Migrate::class)->isMigrationNeeded()) {
            $args['migrations'] = glsr(Migrate::class)->pendingVersions();
        }
        if (empty($args)) {
            return false;
        }
        glsr(Queue::class)->once(time() + 30, 'queue/migration', $args);
        return true;
    }

    protected function isDismissible(): bool
    {
        return false;
    }
}
