<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;

class SanitizeTermIds extends ArraySanitizer
{
    public function run(): array
    {
        return Arr::uniqueInt(
            array_map([Helper::class, 'getTermTaxonomyId'], $this->value())
        );
    }
}
