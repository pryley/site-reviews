<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

use GeminiLabs\SiteReviews\Helpers\Cast;

class SanitizeMin extends IntSanitizer
{
    public function run(): int
    {
        $min = Cast::toInt($this->args[0]);
        return max($min, $this->value());
    }
}
