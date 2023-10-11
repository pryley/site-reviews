<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Migrate;

class OptionManager
{
    /**
     * Get all of the settings.
     */
    public function all(): array
    {
        if ($settings = Arr::consolidate(glsr()->retrieve('settings'))) {
            return $settings;
        }
        return $this->reset();
    }

    public static function databaseKey(?int $version = null): string
    {
        $versions = static::settingKeys();
        if (null === $version) {
            $version = glsr()->version('major');
        }
        if (array_key_exists($version, $versions)) {
            return $versions[$version];
        }
        return '';
    }

    public function delete(string $path): bool
    {
        return $this->set(Arr::remove($this->all(), $path));
    }

    public static function flushCache(): void
    {
        $alloptions = wp_load_alloptions(true);
        $flushed = false;
        foreach (static::settingKeys() as $option) {
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
     * @param string $path
     * @param mixed $fallback
     * @param string $cast
     * @return mixed
     */
    public function get($path = '', $fallback = '', $cast = '')
    {
        $option = Arr::get($this->all(), $path, $fallback);
        $path = ltrim(Str::removePrefix($path, 'settings'), '.');
        if (!empty($path)) {
            $path = str_replace('.', '/', $path);
            $option = glsr()->filter('option/'.$path, $option);
        }
        return Cast::to($cast, $option);
    }

    /**
     * @param string $path
     */
    public function getArray($path, array $fallback = []): array
    {
        return $this->get($path, $fallback, 'array');
    }

    /**
     * @param string $path
     */
    public function getBool($path, bool $fallback = false): bool
    {
        return $this->get($path, $fallback, 'bool');
    }

    /**
     * @param string $path
     */
    public function getInt($path, int $fallback = 0): int
    {
        return $this->get($path, $fallback, 'int');
    }

    /**
     * @param mixed $fallback
     * @param string $cast
     * @return mixed
     */
    public function getWP(string $path, $fallback = '', $cast = '')
    {
        $option = get_option($path, $fallback);
        return Cast::to($cast, Helper::ifEmpty($option, $fallback, $strict = true));
    }

    /**
     * JSON encoded string of the settings.
     */
    public function json(): string
    {
        return json_encode($this->all(), JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Restricts the provided settings keys to the defaults.
     */
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

    /**
     * Get the uncached settings of the previously saved version.
     */
    public function previous(): ?array
    {
        static::flushCache();
        foreach (static::settingKeys() as $version => $option) {
            if ($settings = Arr::consolidate(get_option($option))) {
                return $settings;
            }
        }
        return null;
    }

    /**
     * Reset the settings to the defaults.
     */
    public function reset(): array
    {
        $settings = Arr::consolidate($this->getWP(static::databaseKey(), []));
        if (empty($settings)) {
            delete_option(static::databaseKey());
            $settings = Arr::consolidate(glsr()->defaults);
            glsr(Migrate::class)->reset(); // Do this to migrate any previous version settings
        }
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

    public static function settingKeys(): array
    {
        $keys = [];
        $version = intval(glsr()->version('major')) + 1;
        while (--$version) {
            if (1 === $version) {
                $keys[$version] = 'geminilabs_site_reviews_settings';
            } elseif (2 === $version) {
                $keys[$version] = 'geminilabs_site_reviews-v2';
            } else {
                $keys[$version] = Str::snakeCase(glsr()->id.'-v'.intval($version));
            }
        }
        return $keys;
    }
}
