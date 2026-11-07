<?php

namespace GeminiLabs\SiteReviews\Controllers\Api\Version1;

use GeminiLabs\SiteReviews\Commands\ChangeLogLevel;
use GeminiLabs\SiteReviews\Commands\ClearConsole;
use GeminiLabs\SiteReviews\Commands\ConfigureIpAddressProxy;
use GeminiLabs\SiteReviews\Commands\ConvertTableEngine;
use GeminiLabs\SiteReviews\Commands\DeactivatePlugin;
use GeminiLabs\SiteReviews\Commands\DetectIpAddress;
use GeminiLabs\SiteReviews\Commands\MigratePlugin;
use GeminiLabs\SiteReviews\Commands\RepairPermissions;
use GeminiLabs\SiteReviews\Controllers\NoticeController;
use GeminiLabs\SiteReviews\Modules\Console;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Modules\SystemInfo;
use GeminiLabs\SiteReviews\Modules\UpdateLock;
use GeminiLabs\SiteReviews\Notices\AbstractNotice;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Rollback;

/**
 * The plugin installation: console, migrations, permissions, tables,
 * IP address detection, system info, rollback, etc.
 */
class RestPluginController extends AbstractRestController
{
    /** @var array<string, AbstractNotice|null> the notices looked up this request */
    protected array $notices = [];

    public function changeConsoleLevel(\WP_REST_Request $request): \WP_REST_Response
    {
        return $this->handle(new ChangeLogLevel(new Request(['level' => $request['level']])));
    }

    /**
     * @return true|\WP_Error
     */
    public function checkConsolePermission()
    {
        return $this->permission(glsr()->getPermission('tools', 'console'));
    }

    /**
     * @return true|\WP_Error
     */
    public function checkFeedbackPermission()
    {
        return $this->permission('activate_plugins');
    }

    /**
     * A dismissal is stored in the logged-in user's meta.
     *
     * @return true|\WP_Error
     */
    public function checkNoticePermission(\WP_REST_Request $request)
    {
        if (!is_user_logged_in()) {
            return $this->refuse('not_logged_in', _x('Sorry, you are not allowed to do that.', 'admin-text', 'site-reviews'), 401);
        }
        if (!$this->notice($request['key'])) {
            return $this->refuse('invalid_notice', _x('Invalid notice.', 'admin-text', 'site-reviews'), 404);
        }
        return true;
    }

    /**
     * @return true|\WP_Error
     */
    public function checkPermissionsPermission()
    {
        return $this->permission('edit_users');
    }

    /**
     * @return true|\WP_Error
     */
    public function checkRollbackPermission()
    {
        if (!glsr()->can('update_plugins')) {
            return $this->refuse('forbidden',
                _x('You do not have permission to rollback the plugin.', 'admin-text', 'site-reviews'),
                rest_authorization_required_code()
            );
        }
        if (glsr(UpdateLock::class)->isLocked(glsr()->basename)) {
            return $this->refuse('rollback_locked',
                _x('Rollback is unavailable because the Site Reviews folder is a git repository.', 'admin-text', 'site-reviews'),
                409
            );
        }
        return true;
    }

    /**
     * @return true|\WP_Error
     */
    public function checkSystemInfoPermission()
    {
        return $this->permission(glsr()->getPermission('tools', 'system-info'));
    }

    public function clearConsole(): \WP_REST_Response
    {
        return $this->handle(new ClearConsole());
    }

    public function configureIpAddressProxy(\WP_REST_Request $request): \WP_REST_Response
    {
        return $this->handle(new ConfigureIpAddressProxy(new Request([
            'custom_http_header' => $request['custom_http_header'],
            'proxy_http_header' => $request['proxy_http_header'],
            'trusted_proxies' => $request['trusted_proxies'],
        ])));
    }

    public function convertTableEngine(\WP_REST_Request $request): \WP_REST_Response
    {
        return $this->handle(new ConvertTableEngine(new Request(['table' => $request['table']])));
    }

    public function detectIpAddress(): \WP_REST_Response
    {
        return $this->handle(new DetectIpAddress());
    }

    /**
     * Dismiss the notice for the logged-in user.
     */
    public function dismissNotice(\WP_REST_Request $request): \WP_REST_Response
    {
        $this->notice($request['key'])->dismiss();
        return $this->respond(['key' => $request['key']]);
    }

    /**
     * Send the deactivation feedback before plugin deactivation.
     */
    public function feedback(\WP_REST_Request $request): \WP_REST_Response
    {
        return $this->handle(new DeactivatePlugin(new Request([
            'details' => $request['details'],
            'reason' => $request['reason'],
            'slug' => $request['slug'],
            'version' => $request['version'],
        ])));
    }

    public function fetchConsole(): \WP_REST_Response
    {
        glsr(Notice::class)->addSuccess(_x('Console reloaded.', 'admin-text', 'site-reviews'));
        return $this->respond([
            'console' => sanitize_textarea_field(glsr(Console::class)->getRaw()),
            'notices' => glsr(Notice::class)->get(),
        ]);
    }

    /**
     * @return \WP_REST_Response|\WP_Error
     */
    public function fetchSystemInfo()
    {
        require_once \ABSPATH.'wp-admin/includes/file.php';
        require_once \ABSPATH.'wp-admin/includes/misc.php';
        require_once \ABSPATH.'wp-admin/includes/update.php';
        try {
            $systemInfo = glsr(SystemInfo::class)->get();
        } catch (\Throwable $e) {
            glsr_log()->error($e->getMessage());
            return $this->refuse('system_info_failed', _x('Unable to fetch the System Info.', 'admin-text', 'site-reviews'), 500);
        }
        return $this->respond([
            'system_info' => sanitize_textarea_field($systemInfo),
        ]);
    }

    public function migratePlugin(\WP_REST_Request $request): \WP_REST_Response
    {
        return $this->handle(new MigratePlugin(new Request(['alt' => $request['all']])));
    }

    public function registerRoutes(): void
    {
        register_rest_route($this->restNamespace(), '/plugin/console', [
            [
                'callback' => [$this, 'fetchConsole'],
                'methods' => \WP_REST_Server::READABLE,
                'permission_callback' => [$this, 'checkConsolePermission'],
            ],
            [
                'callback' => [$this, 'clearConsole'],
                'methods' => \WP_REST_Server::DELETABLE,
                'permission_callback' => [$this, 'checkConsolePermission'],
            ],
        ]);
        register_rest_route($this->restNamespace(), '/plugin/console/level', [
            [
                'args' => [
                    'level' => [
                        'required' => true,
                        'type' => 'integer',
                    ],
                ],
                'callback' => [$this, 'changeConsoleLevel'],
                'methods' => 'PUT',
                'permission_callback' => [$this, 'checkConsolePermission'],
            ],
        ]);
        register_rest_route($this->restNamespace(), '/plugin/feedback', [
            [
                'args' => [
                    'details' => [
                        'default' => '',
                        'type' => 'string',
                    ],
                    'reason' => [
                        'required' => true,
                        'type' => 'string',
                    ],
                    'slug' => [
                        'default' => '',
                        'type' => 'string',
                    ],
                    'version' => [
                        'default' => '',
                        'type' => 'string',
                    ],
                ],
                'callback' => [$this, 'feedback'],
                'methods' => \WP_REST_Server::CREATABLE,
                'permission_callback' => [$this, 'checkFeedbackPermission'],
            ],
        ]);
        register_rest_route($this->restNamespace(), '/plugin/ip-address', [
            [
                'callback' => [$this, 'detectIpAddress'],
                'methods' => \WP_REST_Server::READABLE,
                'permission_callback' => [$this, 'checkSettingsPermission'],
            ],
        ]);
        register_rest_route($this->restNamespace(), '/plugin/ip-address/proxy', [
            [
                'args' => [
                    'custom_http_header' => [
                        'default' => '',
                        'type' => 'string',
                    ],
                    'proxy_http_header' => [
                        'default' => '',
                        'type' => 'string',
                    ],
                    'trusted_proxies' => [
                        'default' => '',
                        'type' => 'string',
                    ],
                ],
                'callback' => [$this, 'configureIpAddressProxy'],
                'methods' => 'PUT',
                'permission_callback' => [$this, 'checkSettingsPermission'],
            ],
        ]);
        register_rest_route($this->restNamespace(), '/plugin/migrations', [
            [
                'args' => [
                    'all' => [
                        'default' => false,
                        'type' => 'boolean',
                    ],
                ],
                'callback' => [$this, 'migratePlugin'],
                'methods' => \WP_REST_Server::CREATABLE,
                'permission_callback' => [$this, 'checkSettingsPermission'],
            ],
        ]);
        register_rest_route($this->restNamespace(), '/plugin/notices/(?P<key>[a-z0-9-]+)', [
            [
                'callback' => [$this, 'dismissNotice'],
                'methods' => \WP_REST_Server::DELETABLE,
                'permission_callback' => [$this, 'checkNoticePermission'],
            ],
            [
                'args' => [
                    'until' => [
                        'enum' => ['interval'], // the notice interval
                        'required' => true,
                        'type' => 'string',
                    ],
                ],
                'callback' => [$this, 'snoozeNotice'],
                'methods' => 'PUT',
                'permission_callback' => [$this, 'checkNoticePermission'],
            ],
        ]);
        register_rest_route($this->restNamespace(), '/plugin/permissions', [
            [
                'args' => [
                    'all' => [
                        'default' => false,
                        'type' => 'boolean',
                    ],
                ],
                'callback' => [$this, 'repairPermissions'],
                'methods' => 'PUT',
                'permission_callback' => [$this, 'checkPermissionsPermission'],
            ],
        ]);
        register_rest_route($this->restNamespace(), '/plugin/rollback', [
            [
                'args' => [
                    'version' => [
                        'pattern' => '^\d+\.\d+\.\d+$',
                        'required' => true,
                        'type' => 'string',
                    ],
                ],
                'callback' => [$this, 'rollback'],
                'methods' => \WP_REST_Server::CREATABLE,
                'permission_callback' => [$this, 'checkRollbackPermission'],
            ],
        ]);
        register_rest_route($this->restNamespace(), '/plugin/system-info', [
            [
                'callback' => [$this, 'fetchSystemInfo'],
                'methods' => \WP_REST_Server::READABLE,
                'permission_callback' => [$this, 'checkSystemInfoPermission'],
            ],
        ]);
        register_rest_route($this->restNamespace(), '/plugin/tables/(?P<table>[a-zA-Z0-9_]+)/engine', [
            [
                'callback' => [$this, 'convertTableEngine'],
                'methods' => 'PUT',
                'permission_callback' => [$this, 'checkSettingsPermission'],
            ],
        ]);
    }

    public function repairPermissions(\WP_REST_Request $request): \WP_REST_Response
    {
        $command = new RepairPermissions(new Request(['alt' => $request['all']]));
        $command->handle();
        if ($command->successful()) {
            $reloadLink = glsr(Builder::class)->a([
                'href' => 'javascript:window.location.reload(1)',
                'text' => _x('reload the page', 'admin-text', 'site-reviews'),
            ]);
            glsr(Notice::class)->clear()->addSuccess(
                /* translators: %s: a link with the text "reload the page" */
                sprintf(_x('The permissions have been repaired, please %s for them to take effect.', 'admin-text', 'site-reviews'), $reloadLink)
            );
        }
        return $this->respond($command->response(), $command->successful() ? 200 : 400);
    }

    /**
     * Store the version to roll back to and answer with WordPress's update request.
     */
    public function rollback(\WP_REST_Request $request): \WP_REST_Response
    {
        return $this->respond(glsr(Rollback::class)->rollbackData($request['version']));
    }

    /**
     * Dismiss the notice for the logged-in user until its interval has passed.
     */
    public function snoozeNotice(\WP_REST_Request $request): \WP_REST_Response
    {
        $this->notice($request['key'])->dismiss(['version' => '']);
        return $this->respond(['key' => $request['key']]);
    }

    protected function notice(string $key): ?AbstractNotice
    {
        if (!array_key_exists($key, $this->notices)) {
            $this->notices[$key] = null;
            foreach (glsr(NoticeController::class)->noticeClasses() as $class) {
                $notice = glsr($class);
                if ($notice->key() === $key) {
                    $this->notices[$key] = $notice;
                    break;
                }
            }
        }
        return $this->notices[$key];
    }
}
