<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Helpers\Arr;

Trait Storage
{
    /**
     * @var array
     */
    protected $storage = [];

    /**
     * @param string $property
     * @param mixed $value
     * @param string $key
     * @return false|array
     */
    public function append($property, $value, $key = null)
    {
        $stored = $this->retrieve($property, []);
        if (is_array($stored)) {
            return false;
        }
        if ($key) {
            $stored[$key] = $value;
        } else {
            $stored[] = $value;
        }
        $this->store($stored);
        return $stored;
    }

    /**
     * @param string $property
     * @return mixed
     */
    public function retrieve($property, $fallback = null)
    {
        return Arr::get($this->storage, $property, $fallback);
    }

    /**
     * @param string $property
     * @param string $value
     * @return void
     */
    public function store($property, $value)
    {
        Arr::set($this->storage, $property, $value);
    }
}
