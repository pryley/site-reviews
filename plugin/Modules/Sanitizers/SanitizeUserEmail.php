<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

class SanitizeUserEmail extends StringSanitizer
{
    public function run(): string
    {
        $value = $this->value();
        if (defined('WP_IMPORTING')) {
            return $value;
        }
        if (empty($value)) {
            $user = wp_get_current_user();
            if ($user->exists()) {
                return sanitize_email($user->user_email);
            }
        }
        return $value;
    }

    protected function value(): string
    {
        return (new SanitizeEmail($this->value))->run();
    }
}
