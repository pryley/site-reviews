<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

use GeminiLabs\SiteReviews\Helper;

class SanitizePostId extends IntSanitizer
{
    public function run(): int
    {
        return $this->value();
    }

    protected function value(): int
    {
        return Helper::getPostId($this->value);
    }
}
