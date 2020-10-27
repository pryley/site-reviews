<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Database\DefaultsManager;
use GeminiLabs\SiteReviews\Defaults\PermissionDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Role;
use ReflectionClass;

/**
 * @property array $addons
 * @property string $capability
 * @property string $cron_event
 * @property string $defaults
 * @property string $export_key
 * @property string $file
 * @property string $id
 * @property string $languages
 * @property string $name
 * @property string $paged_handle
 * @property string $paged_query_var
 * @property string $post_type
 * @property string $prefix
 * @property array $reviewTypes
 * @property array $session
 * @property \GeminiLabs\SiteReviews\Arguments $storage
 * @property string $taxonomy
 * @property string $version
 * @property string $testedTo;
 */
final class Application extends Container
{
    use Plugin;
    use Session;
    use Storage;

    const CRON_EVENT = 'site-reviews/schedule/session/purge';
    const EXPORT_KEY = '_glsr_export';
    const ID = 'site-reviews';
    const PAGED_HANDLE = 'pagination_request';
    const PAGED_QUERY_VAR = 'reviews-page'; // filtered
    const POST_TYPE = 'site-review';
    const PREFIX = 'glsr_';
    const TAXONOMY = 'site-review-category';

    protected $addons = [];
    protected $defaults;
    protected $name;
    protected $reviewTypes;

    /**
     * @return void
     */
    public function activate()
    {
        $this->scheduleCronJob();
        add_option(static::PREFIX.'activated', true);
        $this->make(Role::class)->resetAll();
    }

    /**
     * @param string $view
     * @return string
     */
    public function build($view, array $data = [])
    {
        ob_start();
        $this->render($view, $data);
        return ob_get_clean();
    }

    /**
     * @param string $capability
     * @return bool
     */
    public function can($capability)
    {
        return $this->make(Role::class)->can($capability);
    }

    /**
     * @return void
     */
    public function catchFatalError()
    {
        $error = error_get_last();
        if (E_ERROR === Arr::get($error, 'type') && Str::contains($this->path(), Arr::get($error, 'message'))) {
            glsr_log()->error($error['message']);
        }
    }

    /**
     * @return void
     */
    public function deactivate()
    {
        $this->unscheduleCronJob();
    }

    /**
     * @param string $view
     * @return void|string
     */
    public function file($view)
    {
        $view .= '.php';
        $filePaths = [];
        if (Str::startsWith('templates/', $view)) {
            $filePaths[] = $this->themePath(Str::removePrefix($view, 'templates/'));
        }
        $filePaths[] = $this->path($view);
        $filePaths[] = $this->path('views/'.$view);
        foreach ($filePaths as $file) {
            if (file_exists($file)) {
                return $file;
            }
        }
    }

    /**
     * This returns void if run by the plugins_loaded actions hook
     * @return array|void
     * @action plugins_loaded
     */
    public function getDefaultSettings()
    {
        if (empty($this->defaults)) {
            $this->defaults = $this->make(DefaultsManager::class)->get();
        }
        return $this->filterArray('get/defaults', $this->defaults);
    }

    /**
     * @param string $page
     * @param string $tab
     * @return string
     */
    public function getPermission($page = '', $tab = 'index')
    {
        $fallback = 'edit_posts';
        $permissions = $this->make(PermissionDefaults::class)->defaults();
        $permission = Arr::get($permissions, $page, $fallback);
        if (is_array($permission)) {
            $permission = Arr::get($permission, $tab, $fallback);
        }
        return empty($permission) || !is_string($permission)
            ? $fallback
            : $permission;
    }

    /**
     * @param string $page
     * @param string $tab
     * @return bool
     */
    public function hasPermission($page = '', $tab = 'index')
    {
        $isAdmin = $this->isAdmin();
        return !$isAdmin || $this->can($this->getPermission($page, $tab));
    }

    /**
     * @return void
     */
    public function init()
    {
        $this->make(Database::class)->createTables();
        $this->make(Hooks::class)->run();
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return is_admin() && !wp_doing_ajax();
    }

    /**
     * @param object $addon
     * @return void
     */
    public function register($addon)
    {
        try {
            $reflection = new \ReflectionClass($addon);
            if ($id = $reflection->getConstant('ID')) {
                $this->addons[] = $id;
                $this->bind($id, function () use ($addon) {
                    return $addon;
                });
                $addon->init();
            }
        } catch (\ReflectionException $e) {
            glsr_log()->error('Attempted to register an invalid addon.');
        }
    }

    /**
     * @return void
     * @action plugins_loaded
     */
    public function registerAddons()
    {
        $this->action('addon/register', $this);
    }

    /**
     * @return void
     * @action plugins_loaded
     */
    public function registerLanguages()
    {
        load_plugin_textdomain(static::ID, false,
            trailingslashit(plugin_basename($this->path()).'/'.$this->languages)
        );
    }

    /**
     * @return void
     * @action plugins_loaded
     */
    public function registerReviewTypes()
    {
        $types = $this->filterArray('addon/types', []);
        $this->reviewTypes = wp_parse_args($types, [
            'local' => _x('Local Review', 'admin-text', 'site-reviews'),
        ]);
    }

    /**
     * @param string $view
     * @return void
     */
    public function render($view, array $data = [])
    {
        $view = $this->filterString('render/view', $view, $data);
        $file = $this->filterString('views/file', $this->file($view), $view, $data);
        if (!file_exists($file)) {
            glsr_log()->error(sprintf('File not found: (%s) %s', $view, $file));
            return;
        }
        $data = $this->filterArray('views/data', $data, $view);
        extract($data);
        include $file;
    }

    /**
     * @return void
     */
    public function scheduleCronJob()
    {
        if (false === wp_next_scheduled(static::CRON_EVENT)) {
            wp_schedule_event(time(), 'twicedaily', static::CRON_EVENT);
        }
    }

    /**
     * @return void
     * @action admin_init
     */
    public function setDefaultSettings()
    {
        if (get_option(static::PREFIX.'activated')) {
            $this->make(DefaultsManager::class)->set();
            delete_option(static::PREFIX.'activated');
        }
    }

    /**
     * @param string $file
     * @return string
     */
    public function themePath($file = '')
    {
        return get_stylesheet_directory().'/'.static::ID.'/'.ltrim(trim($file), '/');
    }

    /**
     * @return void
     */
    public function unscheduleCronJob()
    {
        wp_unschedule_event(intval(wp_next_scheduled(static::CRON_EVENT)), static::CRON_EVENT);
    }
}
