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

    /**
     * @param int $version
     */
    public static function databaseKey(?int $version = null): string
    {
        if (1 === $version) {
            return 'geminilabs_site_reviews_settings';
        }
        if (2 === $version) {
            return 'geminilabs_site_reviews-v2';
        }
        if (null === $version) {
            $version = glsr()->version('major');
        }
        return Str::snakeCase(glsr()->id.'-v'.intval($version));
    }

    /**
     * @param string $path
     */
    public function delete($path): bool
    {
        return $this->set(Arr::remove($this->all(), $path));
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
            $hook = 'option/'.str_replace('.', '/', $path);
            $option = glsr()->filter($hook, $option);
        }
        return Cast::to($cast, $option);
    }

    /**
     * @param string $path
     * @param array $fallback
     */
    public function getArray($path, $fallback = []): array
    {
        return $this->get($path, $fallback, 'array');
    }

    /**
     * @param string $path
     * @param string|int|bool $fallback
     */
    public function getBool($path, $fallback = false): bool
    {
        return $this->get($path, $fallback, 'bool');
    }

    /**
     * @param string $path
     * @param int $fallback
     */
    public function getInt($path, $fallback = 0): int
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
    public function normalize(array $settings = []): array
    {
        $settings = shortcode_atts(glsr(DefaultsManager::class)->defaults(), Arr::flatten($settings));
        array_walk($settings, function (&$value) {
            if (is_string($value)) {
                $value = wp_kses($value, wp_kses_allowed_html('post'));
            }
        });
        return Arr::convertFromDotNotation($settings);
    }

    /**
     * Get the settings of the previous major version.
     */
    public function previous(): ?array
    {
        $version = intval(glsr()->version('major'));
        while (--$version) {
            if ($settings = Arr::consolidate($this->getWP(static::databaseKey($version)))) {
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
            $pathOrArray = Arr::set($this->all(), $pathOrArray, $value);
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
}
