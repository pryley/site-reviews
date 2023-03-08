<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

class SanitizeUserId extends IntSanitizer
{
    public function run(): int
    {
        $userId = $this->value();
        $userIdFallback = $this->args[0];
        if (get_user_by('ID', $userId)) {
            return $userId;
        }
        if (defined('WP_IMPORTING')) {
            return 0;
        }
        if (is_numeric($userIdFallback)) {
            if (get_user_by('ID', $userIdFallback)) {
                return (int) $userIdFallback;
            }
        }
        return get_current_user_id();
    }
}
