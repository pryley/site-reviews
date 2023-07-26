<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Commands\ChangeLogLevel;
use GeminiLabs\SiteReviews\Commands\ClearConsole;
use GeminiLabs\SiteReviews\Commands\ConvertTableEngine;
use GeminiLabs\SiteReviews\Commands\ExportReviews;
use GeminiLabs\SiteReviews\Commands\ImportReviews;
use GeminiLabs\SiteReviews\Commands\ImportSettings;
use GeminiLabs\SiteReviews\Commands\MigratePlugin;
use GeminiLabs\SiteReviews\Commands\RepairPermissions;
use GeminiLabs\SiteReviews\Commands\RepairReviewRelations;
use GeminiLabs\SiteReviews\Commands\ResetAssignedMeta;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Console;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Migrate;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Modules\SystemInfo;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Rollback;

class ToolsController extends Controller
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
        $success = $this->execute(new ChangeLogLevel($request));
        $notices = glsr(Notice::class)->get();
        if ($success) {
            wp_send_json_success(compact('notices'));
        } else {
            wp_send_json_error(compact('notices'));
        }
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
        $success = $this->execute(new ClearConsole());
        $notices = glsr(Notice::class)->get();
        if ($success) {
            $console = glsr(Console::class)->get();
            wp_send_json_success(compact('console', 'notices'));
        } else {
            wp_send_json_error(compact('notices'));
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
     * @action site-reviews/route/ajax/convert-table-engine
     */
    public function convertTableEngineAjax(Request $request): void
    {
        $success = $this->execute(new ConvertTableEngine($request));
        $notices = glsr(Notice::class)->get();
        if ($success) {
            wp_send_json_success(compact('notices'));
        } else {
            wp_send_json_error(compact('notices'));
        }
    }

    /**
     * @action site-reviews/route/admin/detect-ip-address
     */
    public function detectIpAddress(): void
    {
        $link = glsr(Builder::class)->a([
            'data-expand' => '#faq-ipaddress-incorrectly-detected',
            'href' => glsr_admin_url('documentation', 'faq'),
            'text' => _x('FAQ', 'admin-text', 'site-reviews'),
        ]);
        if ('unknown' === $ipAddress = Helper::getIpAddress()) {
            glsr(Notice::class)->addWarning(sprintf(
                _x('Site Reviews was unable to detect an IP address. To fix this, please see the %s.', 'admin-text', 'site-reviews'),
                $link
            ));
        } else {
            glsr(Notice::class)->addSuccess(sprintf(
                _x('Your detected IP address is %s. If this looks incorrect, please see the %s.', 'admin-text', 'site-reviews'),
                '<code>'.$ipAddress.'</code>', $link
            ));
        }
    }

    /**
     * @action site-reviews/route/ajax/detect-ip-address
     */
    public function detectIpAddressAjax(): void
    {
        $this->detectIpAddress();
        wp_send_json_success([
            'notices' => glsr(Notice::class)->get(),
        ]);
    }

    /**
     * @action site-reviews/route/admin/download-console
     */
    public function downloadConsole(): void
    {
        if (glsr()->hasPermission('tools', 'console')) {
            $this->download(glsr()->id.'-console.txt', glsr(Console::class)->get());
        } else {
            glsr(Notice::class)->addError(
                _x('You do not have permission to download the console.', 'admin-text', 'site-reviews')
            );
        }
    }

    /**
     * @action site-reviews/route/admin/download-system-info
     */
    public function downloadSystemInfo(): void
    {
        if (glsr()->hasPermission('tools', 'system-info')) {
            $this->download(glsr()->id.'-system-info.txt', glsr(SystemInfo::class)->get());
        } else {
            glsr(Notice::class)->addError(
                _x('You do not have permission to download the system info report.', 'admin-text', 'site-reviews')
            );
        }
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
        if (glsr()->hasPermission('settings')) {
            $this->download(glsr()->id.'-settings.json', glsr(OptionManager::class)->json());
        } else {
            glsr(Notice::class)->addError(
                _x('You do not have permission to export the settings.', 'admin-text', 'site-reviews')
            );
        }
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
        if (glsr()->hasPermission('settings')) {
            glsr(Notice::class)->addSuccess(_x('Console reloaded.', 'admin-text', 'site-reviews'));
            wp_send_json_success([
                'console' => glsr(Console::class)->getRaw(), // we don't need to esc_html here
                'notices' => glsr(Notice::class)->get(),
            ]);
        } else {
            glsr(Notice::class)->addError(_x('You do not have permission to reload the console.', 'admin-text', 'site-reviews'));
            wp_send_json_error([
                'notices' => glsr(Notice::class)->get(),
            ]);
        }
    }

    /**
     * @param object $value
     * @return object
     * @filter site_transient_update_plugins
     */
    public function filterUpdatePluginsTransient($value)
    {
        if ($version = get_transient(glsr()->prefix.'rollback_version')) {
            $plugin = plugin_basename(glsr()->file);
            $update = (object) [
                'new_version' => $version,
                'package' => sprintf('https://downloads.wordpress.org/plugin/%s.%s.zip', glsr()->id, $version),
                'slug' => glsr()->id,
            ];
            if (is_object($value)) {
                $value->response[$plugin] = $update;
            }
        }
        return $value;
    }

    /**
     * @action site-reviews/route/admin/import-reviews
     */
    public function importReviews(Request $request): void
    {
        $this->execute(new ImportReviews($request));
    }

    /**
     * @action site-reviews/route/admin/import-settings
     */
    public function importSettings(): void
    {
        $this->execute(new ImportSettings());
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
        $success = $this->execute(new MigratePlugin($request));
        $notices = glsr(Notice::class)->get();
        if ($success) {
            wp_send_json_success(compact('notices'));
        } else {
            wp_send_json_error(compact('notices'));
        }
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
        if ($this->execute(new RepairPermissions($request))) {
            $reloadLink = glsr(Builder::class)->a([
                'text' => _x('reload the page', 'admin-text', 'site-reviews'),
                'href' => 'javascript:window.location.reload(1)',
            ]);
            glsr(Notice::class)->clear()->addSuccess(
                sprintf(_x('The permissions have been repaired, please %s for them to take effect.', 'admin-text', 'site-reviews'), $reloadLink)
            );
            wp_send_json_success([
                'notices' => glsr(Notice::class)->get(),
            ]);
        } else {
            wp_send_json_error([
                'notices' => glsr(Notice::class)->get(),
            ]);
        }
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
        $success = $this->execute(new RepairReviewRelations());
        $notices = glsr(Notice::class)->get();
        if ($success) {
            wp_send_json_success(compact('notices'));
        } else {
            wp_send_json_error(compact('notices'));
        }
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
        $success = $this->execute(new ResetAssignedMeta());
        $notices = glsr(Notice::class)->get();
        if ($success) {
            wp_send_json_success(compact('notices'));
        } else {
            wp_send_json_error(compact('notices'));
        }
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
        glsr(Rollback::class)->rollback($request->version);
    }

    /**
     * @action site-reviews/route/ajax/rollback-<Application::ID>
     */
    public function rollbackPluginAjax(Request $request): void
    {
        if (current_user_can('update_plugins')) {
            wp_send_json_success(
                glsr(Rollback::class)->rollbackData($request->version)
            );
        } else {
            wp_send_json_error([
                'error' => _x('You do not have permission to rollback the plugin.', 'admin-text', 'site-reviews'),
            ]);
        }
    }
}
