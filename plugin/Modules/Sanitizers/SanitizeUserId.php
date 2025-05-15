<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

use GeminiLabs\SiteReviews\Helpers\Arr;

class SanitizeUserId extends IntSanitizer
{
    public function run(): int
    {
        if ($userId = $this->value()) {
            return $userId;
        }
        $userIdFallback = $this->args[0];
        if (is_numeric($userIdFallback)) {
            $user = get_user_by('id', $userIdFallback);
            return Arr::getAs('int', $user, 'ID');
        }
        return !defined('WP_IMPORTING') ? get_current_user_id() : 0;
    }

    protected function value(): int
    {
        if (is_numeric($this->value)) {
            $user = get_user_by('id', $this->value);
            return Arr::getAs('int', $user, 'ID');
        }
        if ('user_id' === $this->value) {
            return !defined('WP_IMPORTING') ? get_current_user_id() : 0;
        }
        if (is_string($this->value)) {
            $user = get_user_by('login', sanitize_user($this->value, true));
            return Arr::getAs('int', $user, 'ID');
        }
        return 0;
    }
}
