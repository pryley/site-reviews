<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
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

    public function __toString(): string
    {
        return serialize($this->toArray());
    }

    /**
     * @param mixed $key
     */
    public function array($key, array $fallback = []): array
    {
        return Arr::consolidate($this->get($key, $fallback));
    }

    /**
     * @param mixed $key
     *
     * @return mixed
     */
    public function cast($key, string $cast, $fallback = null)
    {
        return Cast::to($cast, $this->get($key, $fallback));
    }

    public function exists(string $key): bool
    {
        return $this->offsetExists($key);
    }

    /**
     * @param mixed $key
     * @param mixed $fallback
     *
     * @return mixed
     */
    public function get($key, $fallback = null)
    {
        $value = Arr::get($this->getArrayCopy(), $key, null);
        if (is_null($fallback)) {
            return $value;
        }
        return Helper::ifEmpty($value, $fallback);
    }

    public function isEmpty(): bool
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
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * @param mixed $key
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($key): void
    {
        $storage = $this->getArrayCopy();
        unset($storage[$key]);
        $this->exchangeArray($storage);
    }

    /**
     * @return self
     */
    public function replace(array $data = [])
    {
        $this->exchangeArray($data);
        return $this;
    }

    /**
     * @param mixed $key
     *
     * @return mixed
     */
    public function sanitize($key, string $sanitizer)
    {
        $sanitizers = ['key' => $sanitizer];
        $values = ['key' => $this->get($key)];
        $values = glsr(Sanitizer::class, compact('values', 'sanitizers'))->run();
        return Arr::get($values, 'key');
    }

    /**
     * @param mixed $value
     */
    public function set(string $path, $value): void
    {
        $storage = Arr::set($this->getArrayCopy(), $path, $value);
        $this->exchangeArray($storage);
    }

    /**
     * @param array $args Optional parameter that can be used to change the output
     */
    public function toArray(array $args = []): array
    {
        return Cast::toArrayDeep($this->getArrayCopy());
    }
}
