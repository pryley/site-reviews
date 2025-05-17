<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

use GeminiLabs\SiteReviews\Helpers\Str;

/**
 * This allows lowercase alphannumeric characters, dashes, and underscores.
 * A unique value is generated if result is empty.
 */
class SanitizeIdUnique extends StringSanitizer
{
    public function run(): string
    {
        $value = $this->value();
        if (empty($value)) {
            $value = sanitize_key(base64_encode(random_bytes(12)));
            $value = substr($value, 0, 8);
            $value = Str::prefix($value, $this->args[0] ?: glsr()->prefix);
        }
        return $value;
    }

    protected function value(): string
    {
        return (new SanitizeId($this->value))->run();
    }
}
