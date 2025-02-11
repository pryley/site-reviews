<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Controllers\ToolsController;

class ToolsHooks extends AbstractHooks
{
    public function run(): void
    {
        $this->hook(ToolsController::class, [
            ['changeConsoleLevel', 'site-reviews/route/admin/console-level'],
            ['changeConsoleLevelAjax', 'site-reviews/route/ajax/console-level'],
            ['clearConsole', 'site-reviews/route/admin/clear-console'],
            ['clearConsoleAjax', 'site-reviews/route/ajax/clear-console'],
            ['convertTableEngine', 'site-reviews/route/admin/convert-table-engine'],
            ['convertTableEngineAjax', 'site-reviews/route/ajax/convert-table-engine'],
            ['downloadConsole', 'site-reviews/route/admin/download-console'],
            ['downloadCsvTemplate', 'site-reviews/route/admin/download-csv-template'],
            ['downloadSystemInfo', 'site-reviews/route/admin/download-system-info'],
            ['exportReviews', 'site-reviews/route/admin/export-reviews'],
            ['exportSettings', 'site-reviews/route/admin/export-settings'],
            ['fetchConsole', 'site-reviews/route/admin/fetch-console'],
            ['fetchConsoleAjax', 'site-reviews/route/ajax/fetch-console'],
            ['fetchSystemInfoAjax', 'site-reviews/route/ajax/system-info'],
            ['filterUpdatePluginsTransient', 'site_transient_update_plugins'],
            ['importReviewsAjax', 'site-reviews/route/ajax/import-reviews'],
            ['importSettings', 'site-reviews/route/admin/import-settings'],
            ['ipAddressDetection', 'site-reviews/route/admin/ip-address-detection'],
            ['ipAddressDetectionAjax', 'site-reviews/route/ajax/ip-address-detection'],
            ['migratePlugin', 'site-reviews/route/admin/migrate-plugin'],
            ['migratePluginAjax', 'site-reviews/route/ajax/migrate-plugin'],
            ['repairPermissions', 'site-reviews/route/admin/repair-permissions'],
            ['repairPermissionsAjax', 'site-reviews/route/ajax/repair-permissions'],
            ['repairReviewRelations', 'site-reviews/route/admin/repair-review-relations'],
            ['repairReviewRelationsAjax', 'site-reviews/route/ajax/repair-review-relations'],
            ['resetAssignedMeta', 'site-reviews/route/admin/reset-assigned-meta'],
            ['resetAssignedMetaAjax', 'site-reviews/route/ajax/reset-assigned-meta'],
            ['rollbackPlugin', "update-custom_rollback-{$this->id}"],
            ['rollbackPluginAjax', "site-reviews/route/ajax/rollback-{$this->id}"],
        ]);
    }
}
