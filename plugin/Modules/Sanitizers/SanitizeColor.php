<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

use GeminiLabs\SiteReviews\Helpers\Color;

class SanitizeColor extends StringSanitizer
{
    public function run(): string
    {
        $value = $this->value();
        if (null === Color::new($value)) {
            $value = '';
        }
        return $value;
    }
}
