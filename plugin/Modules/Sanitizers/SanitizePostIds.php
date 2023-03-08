<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;

class SanitizePostIds extends ArraySanitizer
{
    public function run(): array
    {
        return Arr::uniqueInt(
            array_map([Helper::class, 'getPostId'], $this->value())
        );
    }
}
