<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

use GeminiLabs\SiteReviews\Helpers\Color;

class SanitizeColor extends StringSanitizer
{
    public function run(): string
    {
        $value = $this->value();
        if ('' === $value) {
            return ''; // don't log an invalid color error for an empty value
        }
        if (null === Color::new($value)) {
            $value = '';
        }
        return $value;
    }
}
