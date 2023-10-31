<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Migrate;

class OptionManager
{
    public function __call($method, $args)
    {
        if ('getWP' === $method) { // @compat
            return call_user_func_array([$this, 'wp'], $args);
        }
    }

    public function all(): array
    {
        if ($settings = Arr::consolidate(glsr()->retrieve('settings'))) {
            return $settings;
        }
        return $this->reset();
    }

    public static function databaseKey(int $version = null): string
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
     * @return mixed
     */
    public function get(string $path = '', $fallback = '', string $cast = '')
    {
        $option = Arr::get($this->all(), $path, $fallback);
        $path = ltrim(Str::removePrefix($path, 'settings'), '.');
        if (!empty($path)) {
            $path = str_replace('.', '/', $path);
            $option = glsr()->filter('option/'.$path, $option);
        }
        return Cast::to($cast, $option);
    }

    public function getArray(string $path, array $fallback = []): array
    {
        return $this->get($path, $fallback, 'array');
    }

    public function getBool(string $path, bool $fallback = false): bool
    {
        return $this->get($path, $fallback, 'bool');
    }

    public function getInt(string $path, int $fallback = 0): int
    {
        return $this->get($path, $fallback, 'int');
    }

    public function json(): string
    {
        return json_encode($this->all(), JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    public function normalize(array $data = []): array
    {
        $settings = Arr::flatten($data);
        array_walk($settings, function (&$value) {
            if (is_string($value)) {
                $value = wp_kses($value, wp_kses_allowed_html('post'));
            }
        });
        $settings = Arr::convertFromDotNotation($settings);
        $strings = Arr::get($settings, 'settings.strings', []);
        $settings = Arr::flatten($settings);
        $settings = shortcode_atts(glsr(DefaultsManager::class)->defaults(), $settings);
        $settings = Arr::convertFromDotNotation($settings);
        $settings['settings']['strings'] = $strings;
        return $settings;
    }

    public function previous(): ?array
    {
        static::flushSettingsCache();
        foreach (static::databaseKeys() as $version => $option) {
            if ($settings = Arr::consolidate(get_option($option))) {
                return $settings;
            }
        }
        return null;
    }

    public function reset(): array
    {
        $settings = Arr::consolidate($this->wp(static::databaseKey(), [], 'array'));
        if (empty($settings)) {
            delete_option(static::databaseKey());
            glsr(Migrate::class)->reset(); // Do this to migrate any previous version settings
        }
        $settings = $this->normalize($settings);
        glsr()->store('settings', $settings);
        return $settings;
    }

    /**
     * @param string|array $pathOrArray
     * @param mixed $value
     */
    public function set($pathOrArray, $value = ''): bool
    {
        if (is_string($pathOrArray)) {
            $pathOrArray = Arr::set($this->reset(), $pathOrArray, $value);
        }
        if ($settings = Arr::consolidate($pathOrArray)) {
            $result = update_option(static::databaseKey(), $settings);
        }
        if (!empty($result)) {
            $this->reset();
            return true;
        }
        return false;
    }

    /**
     * @param mixed $fallback
     * @return mixed
     */
    public function wp(string $path, $fallback = '', string $cast = '')
    {
        $option = get_option($path, $fallback);
        return Cast::to($cast, Helper::ifEmpty($option, $fallback, $strict = true));
    }
}
