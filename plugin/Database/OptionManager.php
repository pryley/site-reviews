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
            if (1 === $version) {
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
        $option = Arr::get($this->all(), $path, $fallback);
        $path = ltrim(Str::removePrefix($path, 'settings'), '.');
        if (!empty($path)) {
            $path = str_replace('.', '/', $path);
            $option = glsr()->filter("option/{$path}", $option);
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
        return wp_json_encode($all, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    public function normalize(array $data = []): array
    {
        $settings = $this->kses($data);
        $strings = Arr::get($settings, 'settings.strings', []);
        if (!empty(glsr()->settings)) { // prevents a possible infinite loop
            $settings = Arr::flatten($settings);
            $settings = shortcode_atts(glsr(DefaultsManager::class)->defaults(), $settings);
            $settings = Arr::unflatten($settings);
        }
        $settings = Arr::set($settings, 'settings.strings', $strings);
        return $settings;
    }

    public function previous(): array
    {
        static::flushSettingsCache();
        foreach (static::databaseKeys() as $version => $option) {
            if ($settings = Arr::consolidate(get_option($option))) {
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
        if (!update_option(static::databaseKey(), $settings)) {
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
            glsr(Migrate::class)->reset(); // Do this to migrate any previous version settings
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
        if (!update_option(static::databaseKey(), $settings)) {
            return false;
        }
        glsr()->store('settings', $settings);
        return true;
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
}
