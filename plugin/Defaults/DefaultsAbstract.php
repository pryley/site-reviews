<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Sanitizer;
use ReflectionClass;

/**
 * @method array dataAttributes(array $values = [])
 * @method array defaults():
 * @method array filter(array $values = [])
 * @method array merge(array $values = [])
 * @method array restrict(array $values = [])
 * @method array unguardedDataAttributes(array $values = [])
 * @method array unguardedDefaults():
 * @method array unguardedFilter(array $values = [])
 * @method array unguardedMerge(array $values = [])
 * @method array unguardedRestrict(array $values = [])
 */
abstract class DefaultsAbstract
{
    /**
     * The methods that are callable.
     * @var array
     */
    protected $callable = [
        'dataAttributes', 'defaults', 'filter', 'merge', 'restrict',
    ];

    /**
     * The values that should be cast.
     * @var array
     */
    protected $casts = [];

    /**
     * The values that should be guarded.
     * @var array
     */
    protected $guarded = [];

    /**
     * @var bool
     */
    protected $is_guarded = false;

    /**
     * The keys that should be mapped to other keys.
     * @var array
     */
    protected $mapped = [];

    /**
     * The values that should be sanitized.
     * @var array
     */
    protected $sanitize = [];

    /**
     * @param string $name
     * @return void|array
     */
    public function __call($name, array $args = [])
    {
        $this->is_guarded = !Str::startsWith('unguarded', $name);
        $method = Helper::buildMethodName(Str::removePrefix('unguarded', $name));
        if (!method_exists($this, $method) || !in_array($method, $this->callable)) {
            glsr_log()->error("Invalid method [$method].");
            return;
        }
        $defaults = call_user_func_array([$this, $method], $this->mapKeys($args));
        $defaults = $this->guard($defaults);
        $defaults = $this->sanitize($defaults);
        $hookName = (new ReflectionClass($this))->getShortName();
        $hookName = str_replace('Defaults', '', $hookName);
        $hookName = Str::dashCase($hookName);
        return glsr()->filterArray('defaults/'.$hookName, $defaults, $method);
    }

    /**
     * Restrict provided values to defaults, remove empty and unchanged values, 
     * and return data attribute keys with JSON encoded values
     * @return array
     */
    protected function dataAttributes(array $values = [])
    {
        $defaults = $this->flattenArrayValues($this->defaults());
        $values = $this->flattenArrayValues(shortcode_atts($defaults, $values));
        $filtered = $this->guard(array_filter(array_diff_assoc($values, $defaults))); // remove all empty values
        $filteredJson = [];
        foreach ($filtered as $key => $value) {
            $filteredJson['data-'.$key] = !is_scalar($value)
                ? json_encode((array) $value, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
                : $value;
        }
        return $filteredJson;
    }

    /**
     * The default values
     * @return array
     */
    protected function defaults()
    {
        return [];
    }

    /**
     * Merge the provided values with the default values, removing empty string/array values.
     * @return array
     */
    protected function filter(array $values = [])
    {
        return $this->merge(array_filter($values, Helper::class.'::isNotEmpty'));
    }

    /**
     * @return array
     */
    protected function flattenArrayValues(array $values)
    {
        array_walk($values, function (&$value) {
            if (is_array($value)) {
                $value = implode(',', array_filter($value, 'is_scalar'));
            }
        });
        return $values;
    }

    /**
     * Remove guarded key values
     * @return array
     */
    protected function guard(array $values)
    {
        return $this->is_guarded
            ? array_diff_key($values, array_flip($this->guarded))
            : $values;
    }

    /**
     * @return array
     */
    protected function mapKeys(array $args)
    {
        $values = Arr::consolidate(array_shift($args));
        foreach ($this->mapped as $old => $new) {
            if (array_key_exists($old, $values)) {
                $values[$new] = $values[$old];
                unset($values[$old]);
            }
        }
        array_unshift($args, $values);
        return $args;
    }

    /**
     * Merge provided values with the defaults
     * @return array
     */
    protected function merge(array $values = [])
    {
        return $this->parse($values, $this->defaults());
    }

    /**
     * @param mixed $values
     * @param mixed $defaults
     * @return array
     */
    protected function parse($values, $defaults)
    {
        $values = Cast::toArray($values);
        if (!is_array($defaults)) {
            return $values;
        }
        $parsed = $defaults;
        foreach ($values as $key => $value) {
            if (!is_scalar($value) && isset($parsed[$key])) {
                $parsed[$key] = Arr::unique($this->parse($value, $parsed[$key]));
                continue;
            }
            $parsed[$key] = $value;
        }
        return $parsed;
    }

    /**
     * @param mixed $values
     * @return array
     */
    protected function parseRestricted($values, array $defaults)
    {
        $values = Cast::toArray($values);
        $parsed = [];
        foreach ($defaults as $key => $default) {
            if (!array_key_exists($key, $values)) {
                $parsed[$key] = $default;
                continue;
            }
            if (is_array($default)) { // if the default value is supposed to be an array
                $parsed[$key] = $this->parse($values[$key], $default);
                continue;
            }
            $parsed[$key] = $values[$key];
        }
        return $parsed;
    }

    /**
     * Merge the provided values with the defaults, removing all non-default keys
     * @return array
     */
    protected function restrict(array $values = [])
    {
        return $this->parseRestricted($values, $this->defaults());
    }

    /**
     * @return array
     */
    protected function sanitize(array $values = [])
    {
        foreach ($this->casts as $key => $cast) {
            if (array_key_exists($key, $values)) {
                $values[$key] = Cast::to($cast, $values[$key]);
            }
        }
        return (new Sanitizer($values, $this->sanitize))->run();
    }
}
