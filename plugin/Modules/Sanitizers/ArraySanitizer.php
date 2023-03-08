<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

use GeminiLabs\SiteReviews\Helpers\Cast;

abstract class ArraySanitizer extends AbstractSanitizer
{
    abstract public function run(): array;

    protected function value(): array
    {
        return Cast::toArray($this->value);
    }
}
