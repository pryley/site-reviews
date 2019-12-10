<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Database\DefaultsManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Upgrader;

final class Application extends Container
{
    const CAPABILITY = 'edit_others_posts';
    const CRON_EVENT = 'site-reviews/schedule/session/purge';
    const ID = 'site-reviews';
    const PAGED_QUERY_VAR = 'reviews-page';
    const POST_TYPE = 'site-review';
    const PREFIX = 'glsr_';
    const TAXONOMY = 'site-review-category';

    public $defaults;
    public $deprecated = [];
    public $file;
    public $languages;
    public $mceShortcodes = []; //defined elsewhere
    public $name;
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
        $this->make(DefaultsManager::class)->set();
        $this->scheduleCronJob();
        $this->upgrade();
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
            $this->upgrade();
        }
        return apply_filters('site-reviews/get/defaults', $this->defaults);
    }

    /**
     * @return string
     */
    public function getPermission($page = '')
    {
        $permissions = [
            'addons' => 'install_plugins',
            'settings' => 'manage_options',
            // 'welcome' => 'activate_plugins',
        ];
        return Arr::get($permissions, $page, $this->constant('CAPABILITY'));
    }

    /**
     * @return bool
     */
    public function hasPermission($page = '')
    {
        $isAdmin = $this->isAdmin();
        return !$isAdmin || ($isAdmin && current_user_can($this->getPermission($page)));
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
        if (wp_next_scheduled(static::CRON_EVENT)) {
            return;
        }
        wp_schedule_event(time(), 'twicedaily', static::CRON_EVENT);
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
     * @return void
     */
    public function upgrade()
    {
        $this->make(Upgrader::class)->run();
    }

    /**
     * @param mixed $upgrader
     * @return void
     * @action upgrader_process_complete
     */
    public function upgraded($upgrader, array $data)
    {
        if (array_key_exists('plugins', $data)
            && in_array(plugin_basename($this->file), $data['plugins'])
            && 'update' === $data['action']
            && 'plugin' === $data['type']
        ) {
            $this->upgrade();
        }
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
