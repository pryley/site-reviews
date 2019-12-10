<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use ReflectionClass;

abstract class DefaultsAbstract
{
    /**
     * @var array
     */
    protected $callable = [
        'defaults', 'filter', 'merge', 'restrict', 'unguarded',
    ];

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
            return;
        }
        $args[0] = $this->mapKeys(Arr::get($args, 0, []));
        $defaults = call_user_func_array([$this, $name], $args);
        $hookName = (new ReflectionClass($this))->getShortName();
        $hookName = str_replace('Defaults', '', $hookName);
        $hookName = Str::dashCase($hookName);
        return apply_filters('site-reviews/defaults/'.$hookName, $defaults, $name);
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
        return $this->normalize($this->merge(array_filter($values)), $values);
    }

    /**
     * @return string
     */
    protected function filteredJson(array $values = [])
    {
        $defaults = $this->flattenArray(
            array_diff_key($this->defaults(), array_flip($this->guarded))
        );
        $values = $this->flattenArray(
            shortcode_atts($defaults, $values)
        );
        $filtered = array_filter(array_diff_assoc($values, $defaults), function ($value) {
            return !$this->isEmpty($value);
        });
        return json_encode($filtered, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * @return array
     */
    protected function flattenArray(array $values)
    {
        array_walk($values, function (&$value) {
            if (!is_array($value)) {
                return;
            }
            $value = implode(',', $value);
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
        return $this->normalize(wp_parse_args($values, $this->defaults()), $values);
    }

    /**
     * @return array
     */
    protected function normalize(array $values, array $originalValues)
    {
        $values['json'] = $this->filteredJson($originalValues);
        return $values;
    }

    /**
     * @return array
     */
    protected function restrict(array $values = [])
    {
        return $this->normalize(shortcode_atts($this->defaults(), $values), $values);
    }

    /**
     * @return array
     */
    protected function unguarded()
    {
        return array_diff_key($this->defaults(), array_flip($this->guarded));
    }
}
