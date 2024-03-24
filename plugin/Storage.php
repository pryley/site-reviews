<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Helpers\Cast;

trait Storage
{
    /** @var Arguments */
    protected $storage;

    /**
     * @param mixed $value
     */
    public function append(string $property, $value, ?string $key = null): bool
    {
        $stored = $this->retrieve($property, []);
        if (!is_array($stored)) {
            return false;
        }
        if ($key) {
            $stored[$key] = $value;
        } else {
            $stored[] = $value;
        }
        $this->store($property, $stored);
        return true;
    }

    public function discard(string $property): void
    {
        unset($this->storage()->$property);
    }

    /**
     * @param mixed $fallback
     *
     * @return mixed
     */
    public function retrieve(string $property, $fallback = null)
    {
        return $this->storage()->get($property, $fallback);
    }

    /**
     * @param mixed $fallback
     *
     * @return mixed
     */
    public function retrieveAs(string $cast, string $property, $fallback = null)
    {
        return Cast::to($cast, $this->storage()->get($property, $fallback));
    }

    public function storage(): Arguments
    {
        if (!$this->storage instanceof Arguments) {
            $this->storage = new Arguments([]);
        }
        return $this->storage;
    }

    /**
     * @param mixed $value
     */
    public function store(string $property, $value): void
    {
        $this->storage()->set($property, $value);
    }
}
