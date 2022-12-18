<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Sanitizer;

class Arguments extends \ArrayObject
{
    /**
     * @param mixed $args
     */
    public function __construct($args = [])
    {
        if ($args instanceof Arguments) {
            $args = $args->toArray();
        } else {
            $args = Arr::consolidate($args);
        }
        parent::__construct($args, \ArrayObject::STD_PROP_LIST | \ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return serialize($this->toArray());
    }

    /**
     * @param mixed $key
     * @param string $cast
     * @return mixed
     */
    public function cast($key, $cast)
    {
        return Cast::to($cast, $this->get($key));
    }

    /**
     * @param mixed $key
     * @param mixed $fallback
     * @return mixed
     */
    public function get($key, $fallback = null)
    {
        $value = Arr::get($this->getArrayCopy(), $key, null);
        return !is_null($fallback)
            ? Helper::ifEmpty($value, $fallback)
            : $value;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->getArrayCopy());
    }

    /**
     * @return self
     */
    public function merge(array $data = [])
    {
        $storage = wp_parse_args($data, $this->getArrayCopy());
        $this->exchangeArray($storage);
        return $this;
    }

    /**
     * @param mixed $key
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * @param mixed $key
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($key)
    {
        $storage = $this->getArrayCopy();
        unset($storage[$key]);
        $this->exchangeArray($storage);
    }

    /**
     * @param mixed $key
     * @param string $sanitizer
     * @return mixed
     */
    public function sanitize($key, $sanitizer)
    {
        $sanitizers = ['key' => $sanitizer];
        $values = ['key' => $this->get($key)];
        $values = glsr(Sanitizer::class, compact('values', 'sanitizers'))->run();
        return Arr::get($values, 'key');
    }

    /**
     * @param string $path
     * @param mixed $value
     * @return void
     */
    public function set($path, $value)
    {
        $storage = Arr::set($this->getArrayCopy(), $path, $value);
        $this->exchangeArray($storage);
    }

    /**
     * @param string|array $args Optional parameter that can be used to change the output
     * @return array
     */
    public function toArray($args = [])
    {
        return $this->getArrayCopy();
    }
}
