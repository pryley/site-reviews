<?php

namespace GeminiLabs\SiteReviews\Contracts;

interface PartialContract
{
    /**
     * @return string
     */
    public function build(array $args = []);
}
