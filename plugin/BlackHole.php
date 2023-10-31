<?php

namespace GeminiLabs\SiteReviews;

class BlackHole extends \ArrayObject
{
    public $alias;

    public function __construct($alias = '')
    {
        $this->alias = $alias;
        parent::__construct([]);
    }

    public function __get($property): void
    {
        return;
    }

    public function __call(string $method, $args)
    {
        glsr_log()->error("Attempting to call $method() on an unknown class [$this->alias]");
    }
}
