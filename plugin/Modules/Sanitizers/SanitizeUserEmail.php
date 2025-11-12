<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

use GeminiLabs\SiteReviews\Helpers\Cast;

class SanitizeUserEmail extends StringSanitizer
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

    public function run(): string
    {
        $value = $this->sanitizeValue($this->value()); // allow any valid email value
        if (defined('WP_IMPORTING')) {
            return $value;
        }
        if (!empty($value)) {
            return $value;
        }
        $fallback = Cast::toString($this->args[0]); // try the fallback value
        if ('current_user' === $fallback) {
            $fallback = $this->userValue(wp_get_current_user());
        }
        return $this->sanitizeValue($fallback);
    }

    protected function sanitizeValue(string $value): string
    {
        return (new SanitizeEmail($value))->run();
    }

    /**
     * @param mixed $user
     */
    protected function userValue($user): string
    {
        if (!$user instanceof \WP_User) {
            return Cast::toString($user);
        }
        if (!$user->exists()) {
            return '';
        }
        return $user->user_email;
    }
}
