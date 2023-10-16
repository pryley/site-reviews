<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

class SanitizeTextPost extends StringSanitizer
{
    public function run(): string
    {
        return wp_kses(wp_unslash($this->value()), 'post');
    }
}
