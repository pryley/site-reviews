<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;

class Arguments extends \ArrayObject
{
    /**
     * @param array $args
     */
    public function __construct($args)
    {
        $args = Arr::consolidate($args);
        parent::__construct($args, \ArrayObject::STD_PROP_LIST | \ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * @param mixed $key
     * @param mixed $fallback
     * @return mixed
     */
    public function get($key, $fallback = null)
    {
        $value = Arr::get($this->toArray(), $key, $fallback);
        return isset($fallback)
            ? Helper::ifEmpty($value, $fallback)
            : $value;
    }

    /**
     * @return self
     */
    public function merge(array $data = [])
    {
        $storage = wp_parse_args($data, $this->toArray());
        $this->exchangeArray($storage);
        return $this;
    }

    /**
     * @param mixed $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * @param string $path
     * @param mixed $value
     * @return void
     */
    public function set($path, $value)
    {
        $storage = Arr::set($this->toArray(), $path, $value);
        $this->exchangeArray($storage);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->getArrayCopy();
    }
}
