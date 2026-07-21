<?php

namespace GeminiLabs\SiteReviews\Addons;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Contracts\DefaultsContract;
use GeminiLabs\SiteReviews\Contracts\PluginContract;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Plugin;

/**
 * @property string $file
 * @property string $id
 * @property string $languages
 * @property bool   $licensed
 * @property string $name
 * @property string $slug
 * @property string $testedTo
 * @property string $version
 */
abstract class Addon implements PluginContract
{
    use Plugin {
        __construct as protected initPlugin;
        path as protected pluginPath;
        settingPath as protected pluginSettingPath;
        url as protected pluginUrl;
    }

    public const ID = '';
    public const LICENSED = false;
    public const NAME = '';
    public const POST_TYPE = '';
    public const SLUG = '';

    protected ?PluginContract $host = null;
    protected bool $isHost = false;
    protected bool $isModule = false;

    public function __construct(?PluginContract $host = null)
    {
        $this->host = $host; // settings storage routing follows the host, independent of file shape
        $file = wp_normalize_path((new \ReflectionClass($this))->getFileName());
        $derived = dirname(dirname($file)).'/'.$this->id.'.php';
        if (file_exists($derived) || !$host instanceof PluginContract) {
            $this->initPlugin(); // this is a standalone addon
            return;
        }
        $this->basename = $host->basename;
        $this->file = $host->file;
        $this->isModule = true; // this is a premium plugin module
        $this->languages = $host->languages;
        $this->testedTo = $host->testedTo;
        $this->updateUrl = $host->updateUrl;
        $this->uri = $host->uri;
        $reflection = new \ReflectionClass($this);
        $version = $reflection->hasConstant('VERSION')
            ? $reflection->getConstant('VERSION')
            : '';
        $this->version = is_string($version) && '' !== $version
            ? $version // module version fetched at build time
            : $host->version; // fallback to the host version
    }

    public function hostedBy(): ?PluginContract
    {
        return $this->host;
    }

    /**
     * @return static
     */
    public function init()
    {
        $reflection = new \ReflectionClass($this);
        $hooks = Str::replaceLast($reflection->getShortname(), 'Hooks', $reflection->getName());
        if (class_exists($hooks)) {
            glsr()->singleton($hooks);
            glsr($hooks)->run();
            glsr($hooks)->runDeferred();
            glsr($hooks)->runIntegrations();
        } else {
            glsr_log()->error('The '.static::NAME.' addon is missing a Hooks class');
        }
        return $this;
    }

    /**
     * Whether this addon hosts other addon modules.
     */
    public function isHost(): bool
    {
        return $this->isHost;
    }

    public function make(string $class, array $parameters = [])
    {
        $class = Str::camelCase($class);
        $class = ltrim(str_replace([__NAMESPACE__, 'GeminiLabs\SiteReviews'], '', $class), '\\');
        $class = __NAMESPACE__.'\\'.$class;
        return glsr($class, $parameters);
    }

    /**
     * Marks this addon as a host of other addon modules.
     */
    public function markAsHost(): void
    {
        $this->isHost = true;
    }

    /**
     * @param mixed $fallback
     *
     * @return mixed
     */
    public function option(string $path = '', $fallback = '', string $cast = '')
    {
        $path = Str::removePrefix($path, 'settings.');
        $path = Str::prefix($path, $this->settingsPath().'.');
        return glsr_get_option($path, $fallback, $cast);
    }

    /**
     * @param string $defaultsClass a Defaults class used to restrict the options
     */
    public function options(string $defaultsClass = ''): Arguments
    {
        $options = glsr_get_option($this->settingsPath(), [], 'array');
        if (is_a($defaultsClass, DefaultsContract::class, true)) {
            $options = glsr($defaultsClass)->restrict($options);
        }
        return glsr()->args($options);
    }

    public function path(string $file = '', bool $realpath = true): string
    {
        if ($this->isModule) {
            $file = $this->hostedFile($file);
        }
        return $this->pluginPath($file, $realpath);
    }

    public function posts(int $perPage = -1, string $placeholder = ''): array
    {
        if (!$this->hasPostType()) {
            return [];
        }
        $results = glsr(Database::class)->posts([
            'post_status' => 'publish',
            'post_type' => static::POST_TYPE,
            'posts_per_page' => $perPage,
        ]);
        if (!empty($placeholder)) {
            return ['' => $placeholder] + $results;
        }
        return $results;
    }

    /**
     * Accepts the standalone-era spelling too, so `settings.addons.{slug}.x`
     * and a bare `x` both answer `settings.{hostSlug}.{slug}.x` when the addon
     * runs hosted — the same paths OptionManager::get()/set() remap.
     */
    public function settingPath(string $path = ''): string
    {
        $path = Str::removePrefix(trim($path), 'settings.');
        $path = Str::removePrefix($path, 'addons.'.static::SLUG);
        return $this->pluginSettingPath($path);
    }

    /**
     * The addon's mount point inside the composed settings view (without the leading "settings.").
     */
    public function settingsPath(): string
    {
        if ($this->host instanceof PluginContract) {
            return $this->host->slug.'.'.static::SLUG;
        }
        return $this->isHost() ? static::SLUG : 'addons.'.static::SLUG;
    }

    /**
     * The WP option key that holds this addon's settings.
     */
    public function storageKey(): string
    {
        if ($this->host instanceof PluginContract) {
            return Str::snakeCase($this->host->id);
        }
        return Str::snakeCase(static::ID);
    }

    /**
     * The path inside the storage option that holds this addon's values.
     */
    public function storagePath(): string
    {
        if ($this->host instanceof PluginContract) {
            return 'settings.'.static::SLUG;
        }
        return 'settings';
    }

    /**
     * Updates one of this addon's settings; the write is routed to the addon's
     * own storage option (or its host's option when the addon is hosted).
     */
    public function updateOption(string $path, $value = ''): bool
    {
        $path = Str::removePrefix($path, 'settings.');
        $path = Str::prefix($path, $this->settingsPath().'.');
        return glsr(Database\OptionManager::class)->set(Str::prefix($path, 'settings.'), $value);
    }

    public function url(string $path = ''): string
    {
        if ($this->isModule) {
            $path = $this->hostedFile($path);
        }
        return $this->pluginUrl($path);
    }

    /**
     * Maps a standalone-shaped file path to its location inside the host's merged file tree.
     */
    protected function hostedFile(string $file): string
    {
        $file = ltrim(trim($file), '/');
        if ('' === $file) {
            return $file;
        }
        if (str_starts_with($file, 'plugin/')) {
            // Namespaces outside the premium prefix fall back to the last segment
            $namespace = (new \ReflectionClass($this))->getNamespaceName();
            $prefix = 'GeminiLabs\\SiteReviews\\Premium\\';
            $relative = str_starts_with($namespace, $prefix)
                ? substr($namespace, strlen($prefix))
                : substr((string) strrchr($namespace, '\\'), 1);
            return sprintf('plugin/%s/%s', str_replace('\\', '/', $relative), substr($file, strlen('plugin/')));
        }
        // The "settings" config name is slug-mapped,  every other config path passes through as-is
        if ('config/settings.php' === $file) {
            return 'config/settings/'.static::SLUG.'.php';
        }
        if (str_starts_with($file, 'config/')) {
            return $file;
        }
        if ('assets/blocks' === $file || str_starts_with($file, 'assets/blocks/')) {
            return rtrim('assets/blocks/'.static::SLUG.'/'.substr($file, strlen('assets/blocks/')), '/');
        }
        // Shared asset trees are not slug-mapped
        foreach ([
            'assets/css/',
            'assets/images/',
            'assets/integrations/',
            'assets/js/',
            'assets/npm/',
        ] as $shared) {
            if (str_starts_with($file, $shared)) {
                return $file;
            }
        }
        // Per-addon (standalone) assets live under assets/standalone/{slug}/
        if (str_starts_with($file, 'assets/')) {
            return 'assets/standalone/'.static::SLUG.'/'.substr($file, strlen('assets/'));
        }
        foreach (['templates/', 'views/'] as $prefix) {
            if (str_starts_with($file, $prefix)) {
                return $prefix.static::SLUG.'/'.substr($file, strlen($prefix));
            }
        }
        return $file;
    }
}
