<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

use GeminiLabs\SiteReviews\Modules\Rating;

class SanitizeRating extends IntSanitizer
{
    public function run(): int
    {
        return max(Rating::min(), min(Rating::max(), $this->value()));
    }
}
