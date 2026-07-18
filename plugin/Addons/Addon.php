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
        url as protected pluginUrl;
    }

    public const ID = '';
    public const LICENSED = false;
    public const NAME = '';
    public const POST_TYPE = '';
    public const SLUG = '';

    protected ?PluginContract $host = null;
    protected bool $hostedShape = false;
    protected bool $isHost = false;

    public function __construct(?PluginContract $host = null)
    {
        $this->host = $host; // settings storage routing follows the host, independent of file shape
        $file = wp_normalize_path((new \ReflectionClass($this))->getFileName());
        // The addon's main file, derived the same way register() derives it:
        // {two dirs up from Application.php}/{ID}.php. The trait's own
        // str_replace derivation cannot be used for shape DETECTION — for a
        // hosted module at plugin/{Module}/Application.php it matches nothing
        // and answers the (existing) class file itself.
        $derived = dirname(dirname($file)).'/'.$this->id.'.php';
        if (file_exists($derived) || !$host instanceof PluginContract) {
            $this->initPlugin(); // standalone-shaped: derive identity from the addon's own main file
            return;
        }
        // Hosted shape: the addon lives inside its host's file tree (e.g. the
        // merged premium plugin) and has no main file of its own; its identity
        // comes from the host's main file.
        $this->hostedShape = true;
        $this->file = $host->file;
        $this->basename = $host->basename;
        $this->languages = $host->languages;
        $this->testedTo = $host->testedTo;
        $this->updateUrl = $host->updateUrl;
        $this->uri = $host->uri;
        // The VERSION constant is stamped into hosted modules at build time;
        // in the development workspace it does not exist and the host's
        // version is the fallback. hasConstant() first: getConstant() on a
        // missing constant is deprecated.
        $reflection = new \ReflectionClass($this);
        $version = $reflection->hasConstant('VERSION')
            ? $reflection->getConstant('VERSION')
            : '';
        $this->version = is_string($version) && '' !== $version
            ? $version // build-stamped module version
            : $host->version;
    }

    public function path(string $file = '', bool $realpath = true): string
    {
        if ($this->hostedShape) {
            $file = $this->hostedFile($file);
        }
        return $this->pluginPath($file, $realpath);
    }

    public function url(string $path = ''): string
    {
        if ($this->hostedShape) {
            $path = $this->hostedFile($path);
        }
        return $this->pluginUrl($path);
    }

    /**
     * Maps a standalone-shaped file path to its location inside the host's
     * merged file tree. Assets/config/templates/views live in per-slug
     * subdirectories (collision-free by construction); languages resolve in
     * the shared directory because language files are text-domain keyed.
     */
    protected function hostedFile(string $file): string
    {
        $file = ltrim(trim($file), '/');
        if ('' === $file) {
            return $file;
        }
        if (str_starts_with($file, 'plugin/')) {
            // The hosted addon's code lives at plugin/{relative-namespace}/ in
            // the host's tree — e.g. Premium\Features\Alerts -> plugin/Features/Alerts.
            // Namespaces outside the premium prefix fall back to the last segment.
            $namespace = (new \ReflectionClass($this))->getNamespaceName();
            $prefix = 'GeminiLabs\\SiteReviews\\Premium\\';
            $relative = str_starts_with($namespace, $prefix)
                ? substr($namespace, strlen($prefix))
                : substr((string) strrchr($namespace, '\\'), 1);
            return sprintf('plugin/%s/%s', str_replace('\\', '/', $relative), substr($file, strlen('plugin/')));
        }
        // The "settings" config name is fixed by core, so it is slug-mapped;
        // every other config path is addon-authored and passes through as-is
        // (the host's config/ tree is organised by content type, with file
        // ownership tracked by the host's build tooling).
        if ('config/settings.php' === $file) {
            return 'config/settings/'.static::SLUG.'.php';
        }
        if (str_starts_with($file, 'config/')) {
            return $file;
        }
        foreach (['assets/', 'templates/', 'views/'] as $prefix) {
            if (str_starts_with($file, $prefix)) {
                return $prefix.static::SLUG.'/'.substr($file, strlen($prefix));
            }
        }
        return $file;
    }

    public function hostedBy(): ?PluginContract
    {
        return $this->host;
    }

    /**
     * Marks this addon as a host of other addons. Its own values (the feature
     * toggles) are then stored in the top-level "features" key of its option —
     * a SIBLING of settings, so they can never collide with a hosted addon's
     * settings subtree.
     */
    public function markAsHost(): void
    {
        $this->isHost = true;
    }

    /**
     * The WP option key that holds this addon's settings when it runs standalone.
     */
    public static function databaseKey(): string
    {
        return Str::snakeCase(static::ID);
    }

    /**
     * The WP option key that holds this addon's settings. Hosted addons store
     * their settings inside their host's option instead of their own.
     */
    public function storageKey(): string
    {
        if ($this->host instanceof PluginContract) {
            return Str::snakeCase($this->host->id);
        }
        return static::databaseKey();
    }

    /**
     * The path inside the storage option that holds this addon's values.
     * Standalone: the whole "settings" subtree. Hosted: "settings.{slug}"
     * inside the host's option. A host's own values (the feature toggles)
     * live in the top-level "features" key — a sibling of settings, so no
     * hosted addon's slug can ever collide with them.
     */
    public function storagePath(): string
    {
        if ($this->host instanceof PluginContract) {
            return 'settings.'.static::SLUG;
        }
        return $this->isHost ? 'features' : 'settings';
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

    public function make(string $class, array $parameters = [])
    {
        $class = Str::camelCase($class);
        $class = ltrim(str_replace([__NAMESPACE__, 'GeminiLabs\SiteReviews'], '', $class), '\\');
        $class = __NAMESPACE__.'\\'.$class;
        return glsr($class, $parameters);
    }

    /**
     * @param mixed $fallback
     *
     * @return mixed
     */
    public function option(string $path = '', $fallback = '', string $cast = '')
    {
        $path = Str::removePrefix($path, 'settings.');
        $path = Str::prefix($path, 'addons.'.static::SLUG.'.');
        return glsr_get_option($path, $fallback, $cast);
    }

    /**
     * Updates one of this addon's settings; the write is routed to the addon's
     * own storage option (or its host's option when the addon is hosted).
     */
    public function updateOption(string $path, $value = ''): bool
    {
        $path = Str::removePrefix($path, 'settings.');
        $path = Str::prefix($path, 'addons.'.static::SLUG.'.');
        return glsr(\GeminiLabs\SiteReviews\Database\OptionManager::class)->set(Str::prefix($path, 'settings.'), $value);
    }

    /**
     * You can pass a Defaults class which will be used to restrict the options.
     */
    public function options(string $defaultsClass = ''): Arguments
    {
        $options = glsr_get_option('settings.addons.'.static::SLUG, [], 'array');
        if (is_a($defaultsClass, DefaultsContract::class, true)) {
            $options = glsr($defaultsClass)->restrict($options);
        }
        return glsr()->args($options);
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
}
