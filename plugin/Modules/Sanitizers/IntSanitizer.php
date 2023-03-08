<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

use GeminiLabs\SiteReviews\Helpers\Cast;

abstract class IntSanitizer extends AbstractSanitizer
{
    abstract public function run(): int;

    protected function value(): int
    {
        return Cast::toInt($this->value);
    }
}
