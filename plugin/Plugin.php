<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;

/**
 * @property string $id
 * @property string $name
 *
 * @method array  filterArray($hook, ...$args)
 * @method bool   filterBool($hook, ...$args)
 * @method float  filterFloat($hook, ...$args)
 * @method int    filterInt($hook, ...$args)
 * @method object filterObject($hook, ...$args)
 * @method string filterString($hook, ...$args)
 */
trait Plugin
{
    /**
     * @var static|null
     */
    protected static $instance;

    protected $basename;
    protected $file;
    protected $languages;
    protected $testedTo;
    protected $updateUrl;
    protected $uri;
    protected $version;

    public function __call($method, $args)
    {
        $isFilter = str_starts_with($method, 'filter');
        $cast = Str::removePrefix($method, 'filter');
        $to = Helper::buildMethodName('to', $cast);
        if ($isFilter && method_exists(Cast::class, $to)) {
            $filtered = call_user_func_array([$this, 'filter'], $args);
            return Cast::$to($filtered);
        }
        throw new \BadMethodCallException("Method [$method] does not exist.");
    }

    public function __construct()
    {
        $file = wp_normalize_path((new \ReflectionClass($this))->getFileName());
        $this->file = str_replace('plugin/Application', $this->id, $file);
        $this->basename = plugin_basename($this->file);
        $plugin = get_file_data($this->file, [
            'languages' => 'Domain Path',
            'name' => 'Plugin Name',
            'testedTo' => 'Tested up to',
            'uri' => 'Plugin URI',
            'updateUrl' => 'Update URI',
            'version' => 'Version',
        ], 'plugin');
        array_walk($plugin, function ($value, $key) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        });
    }

    public function __get($property)
    {
        $instance = new \ReflectionClass($this);
        if ($instance->hasProperty($property)) {
            $prop = $instance->getProperty($property);
            if ($prop->isPublic() || $prop->isProtected()) {
                return $this->$property;
            }
        }
        $constant = strtoupper($property);
        if ($instance->hasConstant($constant)) {
            return $instance->getConstant($constant);
        }
    }

    /**
     * isset()/empty() on an inaccessible property consult THIS, never __get() — without it
     * they answer false/true regardless of what the property holds, and a guard like
     * !empty(glsr()->settings) can never pass.
     */
    public function __isset($property)
    {
        $instance = new \ReflectionClass($this);
        if ($instance->hasProperty($property)) {
            $prop = $instance->getProperty($property);
            return ($prop->isPublic() || $prop->isProtected()) && $prop->isInitialized($this);
        }
        return $instance->hasConstant(strtoupper($property));
    }

    /**
     * @param mixed ...$args
     */
    public function action(string $hook, ...$args): void
    {
        $prefix = $this->hookPrefix();
        do_action("{$prefix}/action", $hook, $args);
        do_action_ref_array("{$prefix}/{$hook}", $args);
    }

    /**
     * @param mixed $args
     */
    public function args($args = []): Arguments
    {
        return new Arguments($args);
    }

    public function build(string $view, array $data = []): string
    {
        ob_start();
        $this->render($view, $data);
        return trim(ob_get_clean());
    }

    public function catchFatalError(): void
    {
        $error = error_get_last();
        if (E_ERROR === Arr::get($error, 'type') && str_contains(Arr::get($error, 'message'), $this->path())) {
            glsr_log()->error($error['message']);
        }
    }

    public function config(string $name, bool $filtered = true): array
    {
        $path = $this->filterString('config', "config/{$name}.php");
        $configFile = $this->path($path);
        $config = file_exists($configFile)
            ? include $configFile
            : [];
        $config = Arr::consolidate($config);
        // Don't filter the settings config!
        // They can be filtered with the "site-reviews/settings" filter hook
        if ($filtered && !Str::contains($name, ['integrations', 'settings'])) {
            $config = $this->filterArray("config/{$name}", $config);
        }
        return $config;
    }

    /**
     * @return mixed
     */
    public function constant(string $property, string $className = 'static')
    {
        $property = strtoupper($property);
        $constant = "{$className}::{$property}";
        return defined($constant)
            ? $this->filterString("const/{$property}", constant($constant))
            : '';
    }

    public function file(string $view): string
    {
        $view .= '.php';
        $filePaths = [];
        if (str_starts_with($view, 'templates/')) {
            $filePaths[] = $this->themePath(Str::removePrefix($view, 'templates/'));
        }
        $filePaths[] = $this->path($view);
        $filePaths[] = $this->path("views/{$view}");
        foreach ($filePaths as $file) {
            if (file_exists($file)) {
                return $file;
            }
        }
        return '';
    }

    /**
     * @param mixed ...$args
     *
     * @return mixed
     */
    public function filter(string $hook, ...$args)
    {
        $prefix = $this->hookPrefix();
        do_action("{$prefix}/filter", $hook, $args);
        return apply_filters_ref_array("{$prefix}/{$hook}", $args);
    }

    /**
     * @param mixed ...$args
     */
    public function filterArrayUnique(string $hook, ...$args): array
    {
        $prefix = $this->hookPrefix();
        do_action("{$prefix}/filter", $hook, $args);
        $filtered = apply_filters_ref_array("{$prefix}/{$hook}", $args);
        return array_unique(array_filter(Cast::toArray($filtered)));
    }

    /**
     * @param mixed ...$args
     */
    public function filterArrayUniqueInt(string $hook, ...$args): array
    {
        $prefix = $this->hookPrefix();
        do_action("{$prefix}/filter", $hook, $args);
        $filtered = apply_filters_ref_array("{$prefix}/{$hook}", $args);
        return Arr::uniqueInt(Cast::toArray($filtered));
    }

    /**
     * @param mixed ...$args
     */
    public function filterArrayUniqueString(string $hook, ...$args): array
    {
        $prefix = $this->hookPrefix();
        do_action("{$prefix}/filter", $hook, $args);
        $filtered = apply_filters_ref_array("{$prefix}/{$hook}", $args);
        return Arr::uniqueString(Cast::toArray($filtered));
    }

    /**
     * Whether this plugin registers its own post type. The parent plugin always
     * does; only around half of the addons do.
     */
    public function hasPostType(): bool
    {
        return !empty($this->post_type); // the raw constant, unfiltered
    }

    /**
     * What every hook this plugin fires is namespaced with. The plugin id,
     * unless the plugin runs inside another one (see Addons\Addon).
     */
    public function hookPrefix(): string
    {
        return $this->id;
    }

    /**
     * @return static
     */
    public static function load()
    {
        if (empty(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * @param mixed $fallback
     *
     * @return mixed
     */
    public function option(string $path = '', $fallback = '', string $cast = '')
    {
        return glsr_get_option($path, $fallback, $cast);
    }

    /**
     * Resolves a settings path against this plugin's mount in the composed
     * view. For the raw arrays that never reach OptionManager::get()/set():
     * a site-reviews/settings/sanitize callback is handed the composed tree
     * and the submitted form, and both are keyed by the mount.
     */
    public function settingPath(string $path = ''): string
    {
        $path = Str::removePrefix(trim($path), 'settings.');
        return implode('.', array_filter(['settings', $this->settingsPath(), trim($path, '.')]));
    }

    /**
     * The plugin's mount point inside the composed settings view (without the
     * leading "settings."). The core plugin owns the root; addons override
     * this with their own mount (see Addons\Addon::settingsPath()).
     */
    public function settingsPath(): string
    {
        return '';
    }

    public function path(string $file = '', bool $realpath = true): string
    {
        $basedir = plugin_dir_path($this->file);
        if (!$realpath) {
            $basedir = trailingslashit(WP_PLUGIN_DIR).basename(dirname($this->file));
        }
        $basedir = trailingslashit($basedir);
        if (str_starts_with($file, $basedir)) {
            $file = substr($file, strlen($basedir));
        }
        $path = $basedir.ltrim(trim($file), '/');
        return $this->filterString('path', $path, $file);
    }

    public function render(string $view, array $data = []): void
    {
        $view = $this->filterString('render/view', $view, $data);
        $file = $this->filterString('views/file', $this->file($view), $view, $data);
        if (!file_exists($file)) {
            glsr_log()->error(sprintf('File not found: (%s) %s', $view, $file));
            return;
        }
        $data = $this->filterArray('views/data', $data, $view, $file);
        $data = $this->filterArray("views/data/{$view}", $data, $file);
        extract($data);
        include $file;
    }

    /**
     * @param mixed $args
     */
    public function request($args = []): Request
    {
        return new Request($args);
    }

    /**
     * @return mixed|false
     */
    public function runIf(string $className, ...$args)
    {
        return class_exists($className)
            ? call_user_func_array([glsr($className), 'handle'], $args)
            : false;
    }

    public function themePath(string $file = ''): string
    {
        return get_stylesheet_directory().'/'.$this->id.'/'.ltrim(trim($file), '/');
    }

    public function url(string $path = ''): string
    {
        $basedir = plugin_dir_path($this->file);
        if (str_starts_with($path, $basedir)) {
            $path = substr($path, strlen($basedir));
        }
        $url = esc_url(plugin_dir_url($this->file).ltrim(trim($path), '/'));
        return $this->filterString('url', $url, $path);
    }

    public function version(string $versionLevel = ''): string
    {
        return Helper::version($this->version, $versionLevel);
    }
}
