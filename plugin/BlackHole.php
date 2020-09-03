<?php

namespace GeminiLabs\SiteReviews;

class BlackHole
{
    public function __call($method, $args)
    {
        glsr_log()->error("Call to a member function $method() on an unknown class");
    }
}
