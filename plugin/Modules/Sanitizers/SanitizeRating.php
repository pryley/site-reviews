<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

use GeminiLabs\SiteReviews\Modules\Rating;

class SanitizeRating extends IntSanitizer
{
    public function run(): int
    {
        $max = max(1, (int) glsr()->constant('MAX_RATING', Rating::class));
        $min = max(0, (int) glsr()->constant('MIN_RATING', Rating::class));
        return max($min, min($max, $this->value()));
    }
}
