<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

use GeminiLabs\SiteReviews\Helpers\Arr;

class SanitizeUserId extends IntSanitizer
{
    public array $args;
    public $value;
    public array $values;

    public function __construct($value, array $args = [], array $values = [])
    {
        $args = array_pad($args, 2, ''); // minimum of 2 args
        $this->args = $args;
        $this->value = $this->userValue($value);
        $this->values = $values;
    }

    public function run(): int
    {
        if ($userId = $this->value()) {
            return $userId;
        }
        $fallback = $this->args[0];
        if ('current_user' === $fallback) {
            return $this->userValue(wp_get_current_user());
        }
        return $this->userValue($fallback);
    }

    /**
     * @param mixed $value
     */
    protected function userValue($value): int
    {
        if (empty($value)) {
            return 0;
        }
        if ($value instanceof \WP_User) {
            return $value->ID;
        }
        if ('user_id' === $value) {
            if (!defined('WP_IMPORTING')) {
                return get_current_user_id();
            }
        } elseif (is_numeric($value)) {
            if ($user = get_user_by('id', $value)) {
                return $user->ID;
            }
        } elseif (is_string($value)) {
            if ($user = get_user_by('login', sanitize_user($value, true))) {
                return $user->ID;
            }
        }
        return 0;
    }
}
