<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

use GeminiLabs\SiteReviews\Helpers\Cast;

abstract class StringSanitizer extends AbstractSanitizer
{
    abstract public function run(): string;

    protected function value(): string
    {
        return trim(Cast::toString($this->value));
    }
}
