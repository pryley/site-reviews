<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

class SanitizeUserName extends StringSanitizer
{
    public function run(): string
    {
        $value = sanitize_user($this->value(), true);
        if (defined('WP_IMPORTING')) {
            return $value;
        }
        if (empty($value)) {
            $user = wp_get_current_user();
            if ($user->exists()) {
                return $user->display_name;
            }
        }
        return $value;
    }
}
