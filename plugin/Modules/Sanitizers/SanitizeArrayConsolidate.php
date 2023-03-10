<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

use GeminiLabs\SiteReviews\Helpers\Arr;

class SanitizeArrayConsolidate extends ArraySanitizer
{
    public function run(): array
    {
        return Arr::consolidate($this->value);
    }
}
