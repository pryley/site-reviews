<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

use GeminiLabs\SiteReviews\Helpers\Str;

/**
 * This allows lowercase alphannumeric characters, dashes, and underscores.
 * A value is generated if result is empty.
 */
class SanitizeIdHash extends StringSanitizer
{
    public function run(): string
    {
        $value = $this->value();
        if (empty($value)) {
            $value = Str::hash(serialize($this->values), 8);
            $value = Str::prefix($value, $this->args[0] ?: glsr()->prefix);
        }
        return $value;
    }

    protected function value(): string
    {
        return (new SanitizeId($this->value))->run();
    }
}
