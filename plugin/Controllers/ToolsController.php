<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Commands\ChangeLogLevel;
use GeminiLabs\SiteReviews\Commands\ClearConsole;
use GeminiLabs\SiteReviews\Commands\ConfigureIpAddressProxy;
use GeminiLabs\SiteReviews\Commands\ConvertTableEngine;
use GeminiLabs\SiteReviews\Commands\DetectIpAddress;
use GeminiLabs\SiteReviews\Commands\DownloadCsvTemplate;
use GeminiLabs\SiteReviews\Commands\ExportReviews;
use GeminiLabs\SiteReviews\Commands\ImportReviews;
use GeminiLabs\SiteReviews\Commands\ImportReviewsAttachments;
use GeminiLabs\SiteReviews\Commands\ImportReviewsCleanup;
use GeminiLabs\SiteReviews\Commands\ImportSettings;
use GeminiLabs\SiteReviews\Commands\MigratePlugin;
use GeminiLabs\SiteReviews\Commands\ProcessCsvFile;
use GeminiLabs\SiteReviews\Commands\RepairPermissions;
use GeminiLabs\SiteReviews\Commands\RepairReviewRelations;
use GeminiLabs\SiteReviews\Commands\ResetAssignedMeta;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\Console;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Migrate;
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
     * @action site-reviews/route/admin/console-level
     */
    public function changeConsoleLevelAjax(Request $request): void
    {
        $command = $this->execute(new ChangeLogLevel($request));
        wp_send_json([
            'data' => $command->response(),
            'success' => $command->successful(),
        ]);
    }

    /**
     * @action site-reviews/route/admin/clear-console
     */
    public function clearConsole(): void
    {
        $this->execute(new ClearConsole());
    }

    /**
     * @action site-reviews/route/ajax/clear-console
     */
    public function clearConsoleAjax(): void
    {
        $command = $this->execute(new ClearConsole());
        wp_send_json([
            'data' => $command->response(),
            'success' => $command->successful(),
        ]);
    }

    /**
     * @action site-reviews/route/admin/convert-table-engine
     */
    public function convertTableEngine(Request $request): void
    {
        $this->execute(new ConvertTableEngine($request));
    }

    /**
     * @action site-reviews/route/ajax/convert-table-engine
     */
    public function convertTableEngineAjax(Request $request): void
    {
        $command = $this->execute(new ConvertTableEngine($request));
        wp_send_json([
            'data' => $command->response(),
            'success' => $command->successful(),
        ]);
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
     * @action site-reviews/route/admin/fetch-console
     */
    public function fetchConsole(): void
    {
        // This is only done via the AJAX method
    }

    /**
     * @action site-reviews/route/ajax/fetch-console
     */
    public function fetchConsoleAjax(): void
    {
        if (!glsr()->hasPermission('settings')) {
            glsr(Notice::class)->addError(
                _x('You do not have permission to reload the console.', 'admin-text', 'site-reviews')
            );
            wp_send_json_error([
                'notices' => glsr(Notice::class)->get(),
            ]);
        }
        glsr(Notice::class)->addSuccess(
            _x('Console reloaded.', 'admin-text', 'site-reviews')
        );
        wp_send_json_success([
            'console' => glsr(Console::class)->getRaw(), // we don't need to esc_html here
            'notices' => glsr(Notice::class)->get(),
        ]);
    }

    /**
     * @action site-reviews/route/ajax/system-info
     */
    public function fetchSystemInfoAjax(): void
    {
        if (!glsr()->hasPermission('tools', 'system-info')) {
            glsr(Notice::class)->addError(
                _x('Your user role does not have permission to view the system info.', 'admin-text', 'site-reviews')
            );
            wp_send_json_error([
                'notices' => glsr(Notice::class)->get(),
            ]);
        }
        $systemInfo = glsr(SystemInfo::class)->get();
        wp_send_json_success([
            'data' => esc_html($systemInfo),
        ]);
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
        if ($version = get_transient(glsr()->prefix.'rollback_version')) {
            $update = (object) [
                'new_version' => $version,
                'package' => sprintf('https://downloads.wordpress.org/plugin/%s.%s.zip', glsr()->id, $version),
                'slug' => glsr()->id,
            ];
            if (is_object($value)) {
                $value->response[glsr()->basename] = $update;
            }
        }
        return $value;
    }

    /**
     * @action site-reviews/route/ajax/import-reviews
     */
    public function importReviewsAjax(Request $request): void
    {
        if (!glsr()->hasPermission('tools', 'general')) {
            glsr(Notice::class)->addError(
                _x('You do not have permission to import reviews.', 'admin-text', 'site-reviews')
            );
            wp_send_json_error([
                'notices' => glsr(Notice::class)->get(),
            ]);
        }
        $stages = [
            1 => ProcessCsvFile::class,
            2 => ImportReviews::class,
            3 => ImportReviewsAttachments::class,
            4 => ImportReviewsCleanup::class,
        ];
        $stage = $request->cast('stage', 'int');
        if (array_key_exists($stage, $stages)) {
            $command = $this->execute(new $stages[$stage]($request));
            $command->sendJsonResponse();
        }
        wp_send_json_success();
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
            return;
        }
        $this->execute(new ImportSettings());
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
     * @action site-reviews/route/ajax/ip-address-detection
     */
    public function ipAddressDetectionAjax(Request $request): void
    {
        $command = wp_validate_boolean($request->get('alt', 0))
            ? $this->execute(new DetectIpAddress())
            : $this->execute(new ConfigureIpAddressProxy($request));
        wp_send_json_success($command->response());
    }

    /**
     * @action site-reviews/route/admin/migrate-plugin
     */
    public function migratePlugin(Request $request): void
    {
        $this->execute(new MigratePlugin($request));
    }

    /**
     * @action site-reviews/route/ajax/migrate-plugin
     */
    public function migratePluginAjax(Request $request): void
    {
        $command = $this->execute(new MigratePlugin($request));
        wp_send_json([
            'data' => $command->response(),
            'success' => $command->successful(),
        ]);
    }

    /**
     * @action site-reviews/route/admin/repair-permissions
     */
    public function repairPermissions(Request $request): void
    {
        $this->execute(new RepairPermissions($request));
    }

    /**
     * @action site-reviews/route/ajax/repair-permissions
     */
    public function repairPermissionsAjax(Request $request): void
    {
        $command = $this->execute(new RepairPermissions($request));
        if ($command->successful()) {
            $reloadLink = glsr(Builder::class)->a([
                'text' => _x('reload the page', 'admin-text', 'site-reviews'),
                'href' => 'javascript:window.location.reload(1)',
            ]);
            glsr(Notice::class)->clear()->addSuccess(
                sprintf(_x('The permissions have been repaired, please %s for them to take effect.', 'admin-text', 'site-reviews'), $reloadLink)
            );
        }
        wp_send_json([
            'data' => $command->response(),
            'success' => $command->successful(),
        ]);
    }

    /**
     * @action site-reviews/route/admin/repair-review-relations
     */
    public function repairReviewRelations(): void
    {
        $this->execute(new RepairReviewRelations());
    }

    /**
     * @action site-reviews/route/ajax/repair-review-relations
     */
    public function repairReviewRelationsAjax(): void
    {
        $command = $this->execute(new RepairReviewRelations());
        wp_send_json([
            'data' => $command->response(),
            'success' => $command->successful(),
        ]);
    }

    /**
     * @action site-reviews/route/admin/reset-assigned-meta
     */
    public function resetAssignedMeta(): void
    {
        $this->execute(new ResetAssignedMeta());
    }

    /**
     * @action site-reviews/route/ajax/reset-assigned-meta
     */
    public function resetAssignedMetaAjax(): void
    {
        $command = $this->execute(new ResetAssignedMeta());
        wp_send_json([
            'data' => $command->response(),
            'success' => $command->successful(),
        ]);
    }

    /**
     * @action update-custom_rollback-<Application::ID>
     */
    public function rollbackPlugin(): void
    {
        if (!current_user_can('update_plugins')) {
            wp_die(sprintf(_x('Sorry, you are not allowed to rollback %s.', 'Site Reviews (admin-text)', 'site-reviews'), glsr()->name));
        }
        $request = Request::inputGet();
        check_admin_referer($request->action);
        glsr(Rollback::class)->rollback($request->cast('version', 'string'));
    }

    /**
     * @action site-reviews/route/ajax/rollback-<Application::ID>
     */
    public function rollbackPluginAjax(Request $request): void
    {
        if (!current_user_can('update_plugins')) {
            wp_send_json_error([
                'error' => _x('You do not have permission to rollback the plugin.', 'admin-text', 'site-reviews'),
            ]);
        }
        wp_send_json_success(
            glsr(Rollback::class)->rollbackData($request->cast('version', 'string'))
        );
    }
}
