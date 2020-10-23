<?php

namespace GeminiLabs\SiteReviews;

class BlackHole extends \ArrayObject
{
    public function __construct()
    {
        parent::__construct([]);
    }

    public function __get($property)
    {
        return;
    }

    public function __call($method, $args)
    {
        glsr_log()->error("Call to a member function $method() on an unknown class");
    }
}
