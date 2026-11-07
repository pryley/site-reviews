<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Controllers\ToolsController;

class ToolsHooks extends AbstractHooks
{
    public function run(): void
    {
        $this->hook(ToolsController::class, [
            ['changeConsoleLevel', 'site-reviews/route/admin/console-level'],
            ['clearConsole', 'site-reviews/route/admin/clear-console'],
            ['clearRollbackVersion', 'upgrader_process_complete', 1, 2],
            ['convertTableEngine', 'site-reviews/route/admin/convert-table-engine'],
            ['downloadConsole', 'site-reviews/route/admin/download-console'],
            ['downloadCsvTemplate', 'site-reviews/route/admin/download-csv-template'],
            ['downloadSystemInfo', 'site-reviews/route/admin/download-system-info'],
            ['exportReviews', 'site-reviews/route/admin/export-reviews'],
            ['exportSettings', 'site-reviews/route/admin/export-settings'],
            ['filterUpdatePluginsTransient', 'site_transient_update_plugins'],
            ['importSettings', 'site-reviews/route/admin/import-settings'],
            ['ipAddressDetection', 'site-reviews/route/admin/ip-address-detection'],
            ['migratePlugin', 'site-reviews/route/admin/migrate-plugin'],
            ['repairPermissions', 'site-reviews/route/admin/repair-permissions'],
            ['repairReviewRelations', 'site-reviews/route/admin/repair-review-relations'],
            ['resetAssignedMeta', 'site-reviews/route/admin/reset-assigned-meta'],
        ]);
    }
}
