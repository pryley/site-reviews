<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Controllers\QueueController;

class QueueHooks extends AbstractHooks
{
    /**
     * @return void
     */
    public function run()
    {
        $this->hook(QueueController::class, [
            ['cleanupAfterExport', 'site-reviews/queue/export/cleanup'],
            ['recalculateAssignmentMeta', 'site-reviews/queue/recalculate-meta'],
            ['runMigration', 'site-reviews/queue/migration'],
            ['sendNotification', 'site-reviews/queue/notification'],
        ]);
    }
}
