<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;

abstract class ArraySanitizer extends AbstractSanitizer
{
    abstract public function run(): array;

    /**
     * The values, with a comma-separated string among them read as several values.
     */
    protected function splitValue(): array
    {
        $values = [];
        foreach ($this->value() as $value) {
            $values[] = is_string($value) ? Arr::convertFromString($value) : [$value];
        }
        return array_merge([], ...$values);
    }

    protected function value(): array
    {
        return Cast::toArray($this->value);
    }
}
