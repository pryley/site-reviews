<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;

Trait Storage
{
    /**
     * @var array
     */
    protected $storage = [];

    /**
     * @param string $property
     * @return mixed
     */
    public function __get($property)
    {
        return is_null($value = parent::__get($property))
            ? Arr::get($this->storage, $property, null)
            : $value;
    }

    /**
     * @param string $property
     * @param string $value
     * @return void
     */
    public function __set($property, $value)
    {
        Arr::set($this->storage, $property, $value);
    }
}
