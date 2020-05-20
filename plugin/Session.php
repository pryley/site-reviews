<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Helpers\Arr;

Trait Session
{
    /**
     * @var array
     */
    protected $session = [];

    /**
     * @return void
     */
    public function sessionClear()
    {
        $this->session = [];
    }

    /**
     * @return mixed
     */
    public function sessionGet($key, $fallback = '')
    {
        $value = Arr::get($this->session, $key, $fallback);
        unset($this->session[$key]);
        return $value;
    }

    /**
     * @return void
     */
    public function sessionSet($key, $value)
    {
        $this->session[$key] = $value;
    }
}
