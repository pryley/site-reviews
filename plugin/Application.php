<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Database\DefaultsManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;

/**
 * @property string $capability
 * @property string $cron_event
 * @property string $id
 * @property string $paged_query_var
 * @property string $post_type
 * @property string $prefix
 * @property string $taxonomy
 */
final class Application extends Container
{
    const CAPABILITY = 'edit_others_posts';
    const CRON_EVENT = 'site-reviews/schedule/session/purge';
    const ID = 'site-reviews';
    const PAGED_QUERY_VAR = 'reviews-page';
    const POST_TYPE = 'site-review';
    const PREFIX = 'glsr_';
    const TAXONOMY = 'site-review-category';

    public $addons = [];
    public $defaults;
    public $deprecated = [];
    public $file;
    public $languages;
    public $mceShortcodes = []; //defined elsewhere
    public $name;
    public $postTypeColumns = []; // defined elsewhere
    public $reviewTypes;
    public $schemas = []; //defined elsewhere
    public $version;

    public function __construct()
    {
        static::$instance = $this;
        $this->file = realpath(trailingslashit(dirname(__DIR__)).static::ID.'.php');
        $plugin = get_file_data($this->file, [
            'languages' => 'Domain Path',
            'name' => 'Plugin Name',
            'version' => 'Version',
        ], 'plugin');
        array_walk($plugin, function ($value, $key) {
            $this->$key = $value;
        });
    }

    /**
     * @return void
     */
    public function activate()
    {
        $this->scheduleCronJob();
        add_option(static::PREFIX.'activated', true);
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
        if (E_ERROR !== $error['type'] || !Str::contains($error['message'], $this->path())) {
            return;
        }
        glsr_log()->error($error['message']);
    }

    /**
     * @param string $name
     * @return array
     */
    public function config($name)
    {
        $configFile = $this->path('config/'.$name.'.php');
        $config = file_exists($configFile)
            ? include $configFile
            : [];
        return apply_filters('site-reviews/config/'.$name, $config);
    }

    /**
     * @param string $property
     * @return string
     */
    public function constant($property, $className = 'static')
    {
        $constant = $className.'::'.$property;
        return defined($constant)
            ? apply_filters('site-reviews/const/'.$property, constant($constant))
            : '';
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
        $view.= '.php';
        $filePaths = [];
        if (Str::startsWith('templates/', $view)) {
            $filePaths[] = $this->themePath(Str::removePrefix('templates/', $view));
        }
        $filePaths[] = $this->path($view);
        $filePaths[] = $this->path('views/'.$view);
        foreach ($filePaths as $file) {
            if (!file_exists($file)) {
                continue;
            }
            return $file;
        }
    }

    /**
     * @return array
     */
    public function getDefaults()
    {
        if (empty($this->defaults)) {
            $this->defaults = $this->make(DefaultsManager::class)->get();
        }
        return apply_filters('site-reviews/get/defaults', $this->defaults);
    }

    /**
     * @param string $page
     * @param string $tab
     * @return string
     */
    public function getPermission($page = '', $tab = 'index')
    {
        $fallback = 'edit_posts';
        $permissions = [
            'addons' => 'install_plugins',
            'documentation' => [
                'faq' => 'edit_others_posts',
                'functions' => 'manage_options',
                'hooks' => 'edit_others_posts',
                'index' => 'edit_posts',
                'support' => 'edit_others_posts',
            ],
            'settings' => 'manage_options',
            'tools' => [
                'console' => 'edit_others_posts',
                'general' => 'edit_others_posts',
                'index' => 'edit_others_posts',
                'sync' => 'manage_options',
                'system-info' => 'edit_others_posts',
            ]
        ];
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
        return !$isAdmin || ($isAdmin && $this->can($this->getPermission($page, $tab)));
    }

    /**
     * @return void
     */
    public function init()
    {
        $this->make(Actions::class)->run();
        $this->make(Filters::class)->run();
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return is_admin() && !wp_doing_ajax();
    }

    /**
     * @param string $file
     * @return string
     */
    public function path($file = '', $realpath = true)
    {
        $path = plugin_dir_path($this->file);
        if (!$realpath) {
            $path = trailingslashit(WP_PLUGIN_DIR).basename(dirname($this->file));
        }
        $path = trailingslashit($path).ltrim(trim($file), '/');
        return apply_filters('site-reviews/path', $path, $file);
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
                $this->bind($id, $addon);
                $addon->init();
            }
        } catch(\ReflectionException $e) {
            glsr_log()->error('Attempted to register an invalid addon.');
        }
    }

    /**
     * @return void
     */
    public function registerAddons()
    {
        do_action('site-reviews/addon/register', $this);
    }

    /**
     * @return void
     */
    public function registerLanguages()
    {
        load_plugin_textdomain(static::ID, false,
            trailingslashit(plugin_basename($this->path()).'/'.$this->languages)
        );
    }

    /**
     * @return void
     */
    public function registerReviewTypes()
    {
        $types = apply_filters('site-reviews/addon/types', []);
        $this->reviewTypes = wp_parse_args($types, [
            'local' => __('Local', 'site-reviews'),
        ]);
    }

    /**
     * @param string $view
     * @return void
     */
    public function render($view, array $data = [])
    {
        $view = apply_filters('site-reviews/render/view', $view, $data);
        $file = apply_filters('site-reviews/views/file', $this->file($view), $view, $data);
        if (!file_exists($file)) {
            glsr_log()->error('File not found: '.$file);
            return;
        }
        $data = apply_filters('site-reviews/views/data', $data, $view);
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
     */
    public function setDefaults()
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

    /**
     * @param string $path
     * @return string
     */
    public function url($path = '')
    {
        $url = esc_url(plugin_dir_url($this->file).ltrim(trim($path), '/'));
        return apply_filters('site-reviews/url', $url, $path);
    }

    /**
     * @param string $versionLevel
     * @return string
     */
    public function version($versionLevel = '')
    {
        $pattern = '/^v?(\d{1,5})(\.\d++)?(\.\d++)?(.+)?$/i';
        preg_match($pattern, $this->version, $matches);
        switch ($versionLevel) {
            case 'major':
                $version = Arr::get($matches, 1);
                break;
            case 'minor':
                $version = Arr::get($matches, 1).Arr::get($matches, 2);
                break;
            case 'patch':
                $version = Arr::get($matches, 1).Arr::get($matches, 2).Arr::get($matches, 3);
                break;
        }
        return empty($version)
            ? $this->version
            : $version;
    }
}
