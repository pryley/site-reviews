<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

class SanitizeAttrStyle extends StringSanitizer
{
    public function run(): string
    {
        $style = preg_replace('/[^\w%:;,.#"() \-\'\/]/', '', $this->value());
        $style = strtolower($style);
        return esc_attr($style);
    }
}
