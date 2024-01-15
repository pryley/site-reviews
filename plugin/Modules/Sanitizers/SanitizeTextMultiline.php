<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

class SanitizeTextMultiline extends StringSanitizer
{
    public function run(): string
    {
        return $this->kses(sanitize_textarea_field($this->value()));
    }
}
