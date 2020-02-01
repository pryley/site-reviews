<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;

class OptionManager
{
    /**
     * @var array
     */
    protected $options;

    /**
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
        return Str::snakeCase(Application::ID.'-v'.intval($version));
    }

    /**
     * @return array
     */
    public function all()
    {
        if (empty($this->options)) {
            $this->reset();
        }
        return $this->options;
    }

    /**
     * @param string $path
     * @return bool
     */
    public function delete($path)
    {
        $keys = explode('.', $path);
        $last = array_pop($keys);
        $options = $this->all();
        $pointer = &$options;
        foreach ($keys as $key) {
            if (!isset($pointer[$key]) || !is_array($pointer[$key])) {
                continue;
            }
            $pointer = &$pointer[$key];
        }
        unset($pointer[$last]);
        return $this->set($options);
    }

    /**
     * @param string $path
     * @param mixed $fallback
     * @param string $cast
     * @return mixed
     */
    public function get($path = '', $fallback = '', $cast = '')
    {
        $result = Arr::get($this->all(), $path, $fallback);
        return Helper::castTo($cast, $result);
    }

    /**
     * @param string $path
     * @return bool
     */
    public function getBool($path)
    {
        return Helper::castToBool($this->get($path));
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
        if (empty($option)) {
            $option = $fallback;
        }
        return Helper::castTo($cast, $option);
    }

    /**
     * @return string
     */
    public function json()
    {
        return json_encode($this->all());
    }

    /**
     * @return array
     */
    public function normalize(array $options = [])
    {
        $options = wp_parse_args(
            Arr::flattenArray($options),
            glsr(DefaultsManager::class)->defaults()
        );
        array_walk($options, function (&$value) {
            if (!is_string($value)) {
                return;
            }
            $value = wp_kses($value, wp_kses_allowed_html('post'));
        });
        return Arr::convertDotNotationArray($options);
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
     * @return array
     */
    public function reset()
    {
        $options = $this->getWP(static::databaseKey(), []);
        if (!is_array($options) || empty($options)) {
            delete_option(static::databaseKey());
            $options = glsr()->defaults ?: [];
        }
        $this->options = $options;
    }

    /**
     * @param string|array $pathOrOptions
     * @param mixed $value
     * @return bool
     */
    public function set($pathOrOptions, $value = '')
    {
        if (is_string($pathOrOptions)) {
            $pathOrOptions = Arr::set($this->all(), $pathOrOptions, $value);
        }
        if ($result = update_option(static::databaseKey(), (array) $pathOrOptions)) {
            $this->reset();
        }
        return $result;
    }
}
