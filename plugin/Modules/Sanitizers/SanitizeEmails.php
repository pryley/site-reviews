<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

class SanitizeEmails extends StringSanitizer
{
    public function run(): string
    {
        $emails = explode(',', $this->value());
        foreach ($emails as &$email) {
            $email = sanitize_email($email);
        }
        return implode(',', array_filter($emails));
    }
}
