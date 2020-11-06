<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Database\DefaultsManager;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Defaults\PermissionDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Install;

/**
 * @property array $addons
 * @property string $capability
 * @property string $cron_event
 * @property array $defaults
 * @property string $export_key
 * @property string $file
 * @property string $id
 * @property string $languages
 * @property string $name
 * @property string $paged_handle
 * @property string $paged_query_var
 * @property string $post_type
 * @property string $prefix
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

    const EXPORT_KEY = '_glsr_export';
    const ID = 'site-reviews';
    const PAGED_HANDLE = 'pagination_request';
    const PAGED_QUERY_VAR = 'reviews-page'; // filtered
    const POST_TYPE = 'site-review';
    const PREFIX = 'glsr_';
    const TAXONOMY = 'site-review-category';

    /**
     * @var array
     */
    protected $addons = [];

    /**
     * @var array
     */
    protected $defaults;

    /**
     * @var string
     */
    protected $name;

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
     * @param bool $networkDeactivating
     * @return void
     * @callback register_deactivation_hook
     */
    public function deactivate($networkDeactivating)
    {
        $this->make(Install::class)->deactivate($networkDeactivating);
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
        // Ensure the custom database tables exist, this is needed in cases
        // where the plugin has been updated instead of activated.
        if (empty(get_option(static::PREFIX.'db_version'))) {
            $this->make(Install::class)->run();
        }
        $this->make(Hooks::class)->run();
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return (is_admin() || is_network_admin()) && !wp_doing_ajax();
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
    public function storeDefaults()
    {
        if (empty($this->defaults)) {
            $defaults = $this->make(DefaultsManager::class)->get();
            $this->defaults = $this->filterArray('get/defaults', $defaults);
        }
        if (empty(get_option(OptionManager::databaseKey()))) {
            update_option(OptionManager::databaseKey(), $this->defaults);
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
}
