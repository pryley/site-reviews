<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

use GeminiLabs\SiteReviews\Helpers\Cast;

class SanitizeMax extends IntSanitizer
{
    public function run(): int
    {
        $max = Cast::toInt($this->args[0]);
        $value = $this->value();
        return $max > 0 ? min($max, $value) : $value;
    }
}
