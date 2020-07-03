<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use ReflectionClass;

/**
 * @method array defaults():
 * @method array filter(array $values = [])
 * @method array filteredData(array $values = [])
 * @method array merge(array $values = [])
 * @method array restrict(array $values = [])
 * @method array unguarded()
 */
abstract class DefaultsAbstract
{
    /**
     * @var array
     */
    protected $callable = [
        'defaults', 'filter', 'filteredData', 'merge', 'restrict', 'unguarded',
    ];

    /**
     * @var array
     */
    protected $casts = [];

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var array
     */
    protected $mapped = [];

    /**
     * @param string $name
     * @return void|array
     */
    public function __call($name, array $args = [])
    {
        if (!method_exists($this, $name) || !in_array($name, $this->callable)) {
            glsr_log()->error("Invalid method [$name].");
            return;
        }
        $args[0] = $this->mapKeys(Arr::get($args, 0, []));
        $defaults = $this->sanitize(call_user_func_array([$this, $name], $args));
        $hookName = (new ReflectionClass($this))->getShortName();
        $hookName = str_replace('Defaults', '', $hookName);
        $hookName = Str::dashCase($hookName);
        return glsr()->filterArray('defaults/'.$hookName, $defaults, $name);
    }

    /**
     * @return array
     */
    abstract protected function defaults();

    /**
     * @return array
     */
    protected function filter(array $values = [])
    {
        return $this->merge(array_filter($values));
    }

    /**
     * @return array
     */
    protected function filteredData(array $values = [])
    {
        $defaults = $this->flattenArrayValues($this->unguarded());
        $values = $this->flattenArrayValues(shortcode_atts($defaults, $values));
        $filtered = array_filter(array_diff_assoc($values, $defaults), function ($value) {
            return !$this->isEmpty($value);
        });
        $filteredJson = [];
        foreach ($filtered as $key => $value) {
            $filteredJson['data-'.$key] = is_array($value)
                ? json_encode($value, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
                : $value;
        }
        return $filteredJson;
    }

    /**
     * @return array
     */
    protected function flattenArrayValues(array $values)
    {
        array_walk($values, function (&$value) {
            if (is_array($value)) {
                $value = implode(',', $value);
            }
        });
        return $values;
    }

    /**
     * @param mixed $var
     * @return bool
     */
    protected function isEmpty($var)
    {
        return !is_numeric($var) && !is_bool($var) && empty($var);
    }

    /**
     * @return array
     */
    protected function mapKeys(array $args)
    {
        foreach ($this->mapped as $old => $new) {
            if (array_key_exists($old, $args)) {
                $args[$new] = $args[$old];
                unset($args[$old]);
            }
        }
        return $args;
    }

    /**
     * @return array
     */
    protected function merge(array $values = [])
    {
        return $this->parse($values, $this->defaults());
    }

    /**
     * @param mixed $values
     * @return array
     */
    protected function normalize($values)
    {
        if (!is_string($values)) {
            return Arr::consolidate($values);
        }
        $normalized = [];
        wp_parse_str($values, $normalized);
        return $normalized;
    }

    /**
     * @param mixed $values
     * @param mixed $defaults
     * @return array
     */
    protected function parse($values, $defaults)
    {
        $values = $this->normalize($values);
        if (!is_array($defaults)) {
            return $values;
        }
        $parsed = $defaults;
        foreach ($values as $key => $value) {
            if (is_array($value) && isset($parsed[$key])) {
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
    protected function parseRestricted($values, array $pairs)
    {
        $values = $this->normalize($values);
        $parsed = [];
        foreach ($pairs as $key => $default) {
          if (!array_key_exists($key, $values)) {
              $parsed[$key] = $default;
              continue;
          }
          if (is_array($default)) {
              $parsed[$key] = $this->parse($values[$key], $default);
              continue;
          }
          $parsed[$key] = $values[$key];
        }
        return $parsed;
    }

    /**
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
            if (!array_key_exists($key, $values)) {
                continue;
            }
            $values[$key] = Helper::castTo($cast, $values[$key]);
            if ('string' === $cast) {
                $values[$key] = sanitize_key($values[$key]);
            }
        }
        return $values;
    }

    /**
     * @return array
     */
    protected function unguarded()
    {
        return array_diff_key($this->defaults(), array_flip($this->guarded));
    }
}
