<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Addons\Addon;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Migrate;

/**
 * @method array  getArray(string $path = '', $fallback = [])
 * @method bool   getBool(string $path = '', $fallback = false)
 * @method float  getFloat(string $path = '', $fallback = 0.0)
 * @method int    getInt(string $path = '', $fallback = 0)
 * @method string getString(string $path = '', $fallback = '')
 */
class OptionManager
{
    protected static bool $persisting = false;

    /**
     * @return mixed
     */
    public function __call(string $method, array $args = [])
    {
        if (!str_starts_with($method, 'get')) {
            throw new \BadMethodCallException("Method [$method] does not exist.");
        }
        $cast = strtolower((string) substr($method, 3));
        if (!in_array($cast, ['array', 'bool', 'float', 'int', 'string'])) {
            throw new \BadMethodCallException("Method [$method] does not exist.");
        }
        $path = Arr::getAs('string', $args, 0);
        $fallback = Arr::get($args, 1);
        return call_user_func([$this, 'get'], $path, $fallback, $cast);
    }

    public function all(): array
    {
        $settings = Arr::consolidate(glsr()->retrieve('settings'));
        if (empty($settings)) {
            $settings = $this->reset();
        }
        return $settings;
    }

    public function clean(array $data = []): array
    {
        $settings = $this->kses($data);
        if (!empty(glsr()->settings)) { // access the property directly to prevent an infinite loop
            $savedSettings = $settings;
            $defaults = glsr()->defaults();
            $defaults = Arr::flatten($defaults);
            $settings = Arr::flatten($settings);
            $settings = shortcode_atts($defaults, $settings);
            $settings = Arr::unflatten($settings);
            $settings = $this->restoreOrphanedSettings($settings, $savedSettings);
        }
        return $settings;
    }

    /**
     * The WP option key of an addon's own settings option.
     */
    public static function addonKey(string $addonId): string
    {
        return Str::snakeCase($addonId);
    }

    /**
     * The registered addon instances, keyed by slug.
     *
     * @return Addon[]
     */
    public static function addons(): array
    {
        $addons = [];
        foreach (array_keys(glsr()->addons) as $addonId) {
            $addon = glsr($addonId);
            if ($addon instanceof Addon) {
                $addons[$addon->slug] = $addon;
            }
        }
        return $addons;
    }

    public static function databaseKey(?int $version = null): string
    {
        $versions = static::databaseKeys();
        if (null === $version) {
            $version = glsr()->version('major');
        }
        if (array_key_exists($version, $versions)) {
            return $versions[$version];
        }
        return '';
    }

    public static function databaseKeys(): array
    {
        $keys = [];
        $slug = Str::snakeCase(glsr()->id);
        $version = intval(glsr()->version('major')) + 1;
        while (--$version) {
            if ($version >= 7) {
                $keys[$version] = $slug; // remove version from settings key in versions >= 7.0
            } elseif (1 === $version) {
                $keys[$version] = sprintf('geminilabs_%s_settings', $slug);
            } elseif (2 === $version) {
                $keys[$version] = sprintf('geminilabs_%s-v%s', $slug, $version);
            } else {
                $keys[$version] = sprintf('%s_v%s', $slug, $version);
            }
        }
        return $keys;
    }

    public static function flushSettingsCache(): void
    {
        $options = static::databaseKeys();
        foreach (static::addons() as $addon) {
            $options[] = $addon->storageKey();
        }
        $alloptions = wp_load_alloptions(true);
        $flushed = false;
        foreach (array_unique($options) as $option) {
            if (isset($alloptions[$option])) {
                unset($alloptions[$option]);
                $flushed = true;
            }
        }
        if ($flushed) {
            wp_cache_set('alloptions', $alloptions, 'options');
        }
    }

    /**
     * @param mixed $fallback
     *
     * @return mixed
     */
    public function get(string $path = '', $fallback = '', string $cast = '')
    {
        $settings = $this->all();
        $option = Arr::get($settings, $path, $fallback);
        $path = ltrim(Str::removePrefix($path, 'settings'), '.');
        if (!empty($path)) {
            $hook = 'option/'.str_replace('.', '/', $path);
            $option = glsr()->filter($hook, $option, $settings, $path);
        }
        return Cast::to($cast, $option);
    }

    /**
     * This is used when exporting the settings.
     */
    public function json(): string
    {
        $all = $this->all();
        $all['extra'] = glsr()->filterArray('export/settings/extra', []); // allow addons to export additional data
        return (string) wp_json_encode($all, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    public function mergeDefaults(array $defaults): void
    {
        $saved = Arr::consolidate($this->wp(static::databaseKey(), []));
        $saved = $this->compose($saved); // registered addon settings live in their own options
        $defaults = Arr::flatten(Arr::getAs('array', $defaults, 'settings'));
        $settings = Arr::flatten(Arr::getAs('array', $saved, 'settings'));
        if (empty($defaults) || empty(array_diff_key($defaults, $settings))) {
            return;
        }
        $settings = shortcode_atts($defaults, $settings);
        $settings = Arr::unflatten($settings);
        $settings['strings'] = Arr::consolidate(Arr::get($saved, 'settings.strings'));
        $saved['settings'] = $settings;
        $this->replace($saved);
    }

    public function normalize(array $data = []): array
    {
        $settings = $this->kses($data);
        if (!empty(glsr()->settings)) { // access the property directly to prevent an infinite loop
            $defaults = glsr()->defaults();
            $defaults = Arr::flatten($defaults);
            $settings = Arr::flatten($settings);
            $settings = wp_parse_args($settings, $defaults);
            $settings = Arr::unflatten($settings);
        }
        return $settings;
    }

    public function previous(): array
    {
        static::flushSettingsCache();
        foreach (static::databaseKeys() as $version => $databaseKey) {
            if ($version === intval(glsr()->version('major'))) {
                continue;
            }
            $settings = Arr::consolidate(get_option($databaseKey));
            if (!empty(array_filter(Arr::getAs('array', $settings, 'settings')))) {
                return $settings;
            }
        }
        return [];
    }

    public function replace(array $settings): bool
    {
        if (empty($settings)) {
            return false;
        }
        $settings = $this->normalize($settings);
        if (!$this->persist($settings)) {
            return false;
        }
        $this->reset();
        return true;
    }

    public function reset(): array
    {
        $settings = Arr::consolidate($this->wp(static::databaseKey(), []));
        if (empty($settings)) {
            delete_option(static::databaseKey());
        }
        $settings = $this->compose($settings);
        $settings = $this->normalize($settings);
        glsr()->store('settings', $settings);
        return $settings;
    }

    /**
     * @param mixed $value
     */
    public function set(string $path, $value = ''): bool
    {
        $settings = $this->all();
        $settings = Arr::set($settings, $path, $value);
        $settings = $this->normalize($settings);
        if (!$this->persist($settings)) {
            return false;
        }
        glsr()->store('settings', $settings);
        return true;
    }

    public function updateVersion(): void
    {
        $version = $this->get('version', '0.0.0');
        if (glsr()->version !== $version) {
            $this->set('version', glsr()->version);
            $this->set('version_upgraded_from', $version);
        }
    }

    /**
     * @param mixed $fallback
     *
     * @return mixed
     */
    public function wp(string $path, $fallback = '', string $cast = '')
    {
        $option = get_option($path, $fallback);
        return Cast::to($cast, Helper::ifEmpty($option, $fallback, $strict = true));
    }

    public function kses(array $data): array
    {
        $data = Arr::flatten($data);
        array_walk($data, function (&$value) {
            if (is_string($value)) {
                $value = wp_kses($value, wp_kses_allowed_html('post'));
            }
        });
        $data = Arr::unflatten($data);
        return $data;
    }

    /**
     * Splits the addon subtrees out of a composed settings array and writes each
     * to its addon's own option. Returns the remaining (core-only) settings.
     * This is the single write authority for addon settings; it is also used
     * directly by the Settings API sanitize callback, where WP itself persists
     * the returned remainder to the core plugin option.
     */
    public function split(array $settings, bool &$changed = false): array
    {
        $writes = [];
        $versions = [];
        foreach (static::addons() as $slug => $addon) {
            $values = Arr::get($settings, "settings.addons.{$slug}", null);
            if (!is_array($values)) {
                continue;
            }
            $key = $addon->storageKey();
            if (!array_key_exists($key, $writes)) {
                $writes[$key] = Arr::consolidate(get_option($key));
            }
            $subtree = $addon->storageSubtree();
            if ('' !== $subtree) {
                $writes[$key] = Arr::set($writes[$key], "settings.{$subtree}", $values);
            } else {
                $writes[$key]['settings'] = $values;
            }
            $host = $addon->hostedBy();
            $versions[$key] = $host instanceof \GeminiLabs\SiteReviews\Contracts\PluginContract
                ? $host->version
                : $addon->version;
            unset($settings['settings']['addons'][$slug]);
        }
        foreach ($writes as $key => $value) {
            $value['version'] = $versions[$key];
            if (update_option($key, $value, true)) {
                $changed = true;
            }
        }
        return $settings;
    }

    /**
     * Mounts each registered addon's stored settings into the composed view at
     * settings.addons.{slug}. Addons without their own option yet (not migrated)
     * are skipped so any legacy subtree in the core option remains visible.
     */
    protected function compose(array $settings): array
    {
        foreach (static::addons() as $slug => $addon) {
            $stored = get_option($addon->storageKey());
            if (false === $stored) {
                continue;
            }
            $values = Arr::getAs('array', Arr::consolidate($stored), 'settings');
            $subtree = $addon->storageSubtree();
            if ('' !== $subtree) {
                $values = Arr::getAs('array', $values, $subtree);
            }
            $settings = Arr::set($settings, "settings.addons.{$slug}", $values);
        }
        return $settings;
    }

    /**
     * Persists a composed settings array: addon subtrees to their own options
     * (via split), the remainder to the core plugin option.
     */
    /**
     * True while persist() is writing its already-split settings. The
     * settings-form sanitize callback (registered with register_setting, so
     * WP fires it on EVERY update_option of the core key) must stand down
     * for these writes: re-processing them merges the stale composed memo
     * back in and re-splits, clobbering the addon rows just written.
     */
    public static function isPersisting(): bool
    {
        return static::$persisting;
    }

    protected function persist(array $settings): bool
    {
        $changed = false;
        static::$persisting = true;
        try {
            $settings = $this->split($settings, $changed);
            // A write that only touches addon options legitimately leaves the core
            // option unchanged — update_option() returning false there is not a
            // failure, so a successful addon write counts as a persisted change.
            return update_option(static::databaseKey(), $settings, true) || $changed;
        } finally {
            static::$persisting = false;
        }
    }

    /**
     * This restores orphaned settings in cases where addons have been deactivated, etc.
     */
    protected function restoreOrphanedSettings(array $settings, array $saved): array
    {
        $defaults = glsr()->defaults();
        $settings = Arr::set($settings, 'settings.strings', Arr::get($saved, 'settings.strings', []));
        foreach (Arr::get($saved, 'settings.addons', []) as $addon => $values) {
            if (!isset($defaults['settings']['addons'][$addon])) {
                $settings['settings']['addons'][$addon] = $values;
            }
        }
        foreach (Arr::get($saved, 'settings.integrations', []) as $integration => $values) {
            if (!isset($defaults['settings']['integrations'][$integration])) {
                $settings['settings']['integrations'][$integration] = $values;
            }
        }
        foreach (Arr::get($saved, 'settings.licenses', []) as $addon => $value) {
            if (!isset($defaults['settings']['licenses'][$addon])) {
                $settings['settings']['licenses'][$addon] = $value;
            }
        }
        return $settings;
    }
}
