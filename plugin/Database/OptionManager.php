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
     * @return array
     */
    public function all()
    {
        if ($settings = Arr::consolidate(glsr()->retrieve('settings'))) {
            return $settings;
        }
        return $this->reset();
    }

    /**
     * @param int $version
     * @return string
     */
    public static function databaseKey($version = null)
    {
        if (1 == $version) {
            return 'geminilabs_site_reviews_settings';
        }
        if (2 == $version) {
            return 'geminilabs_site_reviews-v2';
        }
        if (null === $version) {
            $version = explode('.', glsr()->version);
            $version = array_shift($version);
        }
        return Str::snakeCase(glsr()->id.'-v'.intval($version));
    }

    /**
     * @param string $path
     * @return bool
     */
    public function delete($path)
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
        return Cast::to($cast, Arr::get($this->all(), $path, $fallback));
    }

    /**
     * @param string $path
     * @param string|int|bool $fallback
     * @return bool
     */
    public function getBool($path, $fallback = false)
    {
        return $this->get($path, $fallback, 'bool');
    }

    /**
     * @param string $path
     * @param mixed $fallback
     * @param string $cast
     * @return mixed
     */
    public function getWP($path, $fallback = '', $cast = '')
    {
        $option = get_option($path, $fallback);
        return Cast::to($cast, Helper::ifEmpty($option, $fallback, $strict = true));
    }

    /**
     * @return bool
     */
    public function isRecaptchaEnabled()
    {
        $integration = $this->get('settings.submissions.recaptcha.integration');
        return 'all' == $integration || ('guest' == $integration && !is_user_logged_in());
    }

    /**
     * @return string
     */
    public function json()
    {
        return json_encode($this->all(), JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
    }

    /**
     * @return array
     */
    public function normalize(array $settings = [])
    {
        $settings = wp_parse_args(Arr::flatten($settings), glsr(DefaultsManager::class)->defaults());
        array_walk($settings, function (&$value) {
            if (is_string($value)) {
                $value = wp_kses($value, wp_kses_allowed_html('post'));
            }
        });
        return Arr::convertFromDotNotation($settings);
    }

    /**
     * @return array
     */
    public function reset()
    {
        $settings = Arr::consolidate(get_option(static::databaseKey()));
        if (empty($settings)) {
            glsr(Migrate::class)->reset();
            delete_option(static::databaseKey());
            $settings = Arr::consolidate(glsr()->defaults);
        }
        glsr()->store('settings', $settings);
        return $settings;
    }

    /**
     * @param string|array $pathOrArray
     * @param mixed $value
     * @return bool
     */
    public function set($pathOrArray, $value = '')
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
