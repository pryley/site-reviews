<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Helpers\Arr;

trait Session
{
    protected array $session = [];

    public function session(): Arguments
    {
        return glsr()->args($this->session);
    }

    public function sessionClear(): void
    {
        $this->session = [];
    }

    /**
     * @param mixed $fallback
     *
     * @return mixed
     */
    public function sessionGet(string $key, $fallback = '')
    {
        return Arr::get($this->session, $key, $fallback);
    }

    /**
     * @param mixed $fallback
     *
     * @return mixed
     */
    public function sessionPluck(string $key, $fallback = '')
    {
        $value = $this->sessionGet($key, $fallback);
        unset($this->session[$key]);
        return $value;
    }

    /**
     * @param mixed $value
     */
    public function sessionSet(string $key, $value): void
    {
        $this->session[$key] = $value;
    }
}
