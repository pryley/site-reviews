<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

class SanitizeUserName extends StringSanitizer
{
    public function run(): string
    {
        $value = $this->value();
        if (defined('WP_IMPORTING')) {
            return $value;
        }
        $user = wp_get_current_user();
        if ($user->exists()) {
            return $user->display_name;
        }
        return $value;
    }

    protected function value(): string
    {
        return (new SanitizeText($this->value))->run();
    }
}
