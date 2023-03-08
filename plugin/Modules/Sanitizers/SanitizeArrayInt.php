<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

use GeminiLabs\SiteReviews\Helpers\Arr;

class SanitizeArrayInt extends ArraySanitizer
{
    public function run(): array
    {
        return Arr::uniqueInt($this->value(), true); // use absint
    }
}
