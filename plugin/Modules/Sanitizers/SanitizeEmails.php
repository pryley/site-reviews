<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

class SanitizeEmails extends StringSanitizer
{
    public function run(): string
    {
        $emails = explode(',', $this->value());
        $emails = array_map(fn ($email) => sanitize_email($email), $emails);
        return implode(',', array_filter($emails));
    }
}
