<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Commands\ChangeLogLevel;
use GeminiLabs\SiteReviews\Commands\ClearConsole;
use GeminiLabs\SiteReviews\Commands\ConfigureIpAddressProxy;
use GeminiLabs\SiteReviews\Commands\ConvertTableEngine;
use GeminiLabs\SiteReviews\Commands\DetectIpAddress;
use GeminiLabs\SiteReviews\Commands\DownloadCsvTemplate;
use GeminiLabs\SiteReviews\Commands\ExportReviews;
use GeminiLabs\SiteReviews\Commands\ImportSettings;
use GeminiLabs\SiteReviews\Commands\MigratePlugin;
use GeminiLabs\SiteReviews\Commands\RepairPermissions;
use GeminiLabs\SiteReviews\Commands\RepairReviewRelations;
use GeminiLabs\SiteReviews\Commands\ResetAssignedMeta;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\Console;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Modules\SystemInfo;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Rollback;

class ToolsController extends AbstractController
{
    /**
     * @action site-reviews/route/admin/console-level
     */
    public function changeConsoleLevel(Request $request): void
    {
        $this->execute(new ChangeLogLevel($request));
    }

    /**
     * @action site-reviews/route/admin/clear-console
     */
    public function clearConsole(): void
    {
        $this->execute(new ClearConsole());
    }

    /**
     * Forget the stored rollback version once WordPress has processed this plugin.
     *
     * @param mixed $upgrader
     *
     * @action upgrader_process_complete:1
     */
    public function clearRollbackVersion($upgrader, array $options): void
    {
        $plugins = (array) ($options['plugins'] ?? []);
        if ('plugin' === ($options['type'] ?? '') && in_array(glsr()->basename, $plugins, true)) {
            delete_transient(glsr()->prefix.'rollback_version');
        }
    }

    /**
     * @action site-reviews/route/admin/convert-table-engine
     */
    public function convertTableEngine(Request $request): void
    {
        $this->execute(new ConvertTableEngine($request));
    }

    /**
     * @action site-reviews/route/admin/download-console
     */
    public function downloadConsole(): void
    {
        if (!glsr()->hasPermission('tools', 'console')) {
            glsr(Notice::class)->addError(
                _x('You do not have permission to download the console.', 'admin-text', 'site-reviews')
            );
            return;
        }
        $this->download(glsr()->id.'-console.txt', glsr(Console::class)->get());
    }

    /**
     * @action site-reviews/route/admin/download-csv-template
     */
    public function downloadCsvTemplate(): void
    {
        $this->execute(new DownloadCsvTemplate());
    }

    /**
     * @action site-reviews/route/admin/download-system-info
     */
    public function downloadSystemInfo(): void
    {
        if (!glsr()->hasPermission('tools', 'system-info')) {
            glsr(Notice::class)->addError(
                _x('You do not have permission to download the system info report.', 'admin-text', 'site-reviews')
            );
            return;
        }
        $this->download(glsr()->id.'-system-info.txt', glsr(SystemInfo::class)->get());
    }

    /**
     * @action site-reviews/route/admin/export-reviews
     */
    public function exportReviews(Request $request): void
    {
        $this->execute(new ExportReviews($request));
    }

    /**
     * @action site-reviews/route/admin/export-settings
     */
    public function exportSettings(): void
    {
        if (!glsr()->hasPermission('settings')) {
            glsr(Notice::class)->addError(
                _x('You do not have permission to export settings.', 'admin-text', 'site-reviews')
            );
            return;
        }
        $settings = glsr(OptionManager::class)->json();
        $this->download(glsr()->id.'-settings.json', $settings);
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     *
     * @filter site_transient_update_plugins
     */
    public function filterUpdatePluginsTransient($value)
    {
        if (!is_object($value) || !glsr(Rollback::class)->isUpdateRequest()) {
            return $value;
        }
        if ($version = get_transient(glsr()->prefix.'rollback_version')) {
            $value->response[glsr()->basename] = (object) [
                'new_version' => $version,
                'package' => sprintf('https://downloads.wordpress.org/plugin/%s.%s.zip', glsr()->id, $version),
                'slug' => glsr()->id,
            ];
        }
        return $value;
    }

    /**
     * @action site-reviews/route/admin/import-settings
     */
    public function importSettings(): void
    {
        if (!glsr()->hasPermission('settings')) {
            glsr(Notice::class)->addError(
                _x('You do not have permission to import settings.', 'admin-text', 'site-reviews')
            );
            glsr(Notice::class)->store(); // because of the page reload
            return;
        }
        $this->execute(new ImportSettings());
        glsr(Notice::class)->store(); // because of the page reload
    }

    /**
     * @action site-reviews/route/admin/ip-address-detection
     */
    public function ipAddressDetection(Request $request): void
    {
        if (wp_validate_boolean($request->get('alt', 0))) {
            $this->execute(new DetectIpAddress());
            return;
        }
        $this->execute(new ConfigureIpAddressProxy($request));
    }

    /**
     * @action site-reviews/route/admin/migrate-plugin
     */
    public function migratePlugin(Request $request): void
    {
        $this->execute(new MigratePlugin($request));
    }

    /**
     * @action site-reviews/route/admin/repair-permissions
     */
    public function repairPermissions(Request $request): void
    {
        $this->execute(new RepairPermissions($request));
    }

    /**
     * @action site-reviews/route/admin/repair-review-relations
     */
    public function repairReviewRelations(): void
    {
        $this->execute(new RepairReviewRelations());
    }

    /**
     * @action site-reviews/route/admin/reset-assigned-meta
     */
    public function resetAssignedMeta(): void
    {
        $this->execute(new ResetAssignedMeta());
    }
}
