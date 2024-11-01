<?php

namespace GeminiLabs\SiteReviews\Database;

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
            $defaults = glsr()->defaults(); // @phpstan-ignore-line
            $defaults = Arr::flatten($defaults);
            $settings = Arr::flatten($settings);
            $settings = shortcode_atts($defaults, $settings);
            $settings = Arr::unflatten($settings);
            $settings = $this->restoreOrphanedSettings($settings, $savedSettings);
        }
        return $settings;
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
        $alloptions = wp_load_alloptions(true);
        $flushed = false;
        foreach (static::databaseKeys() as $option) {
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
            $defaults = glsr()->defaults(); // @phpstan-ignore-line
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
        if (!update_option(static::databaseKey(), $settings, true)) {
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
            // glsr(Migrate::class)->reset(); // Do this to migrate any previous version settings
        }
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
        if (!update_option(static::databaseKey(), $settings, true)) {
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
