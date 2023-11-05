<?php

namespace GeminiLabs\SiteReviews\Contracts;

interface PartialContract
{
    public function build(array $args = []): string;
}
