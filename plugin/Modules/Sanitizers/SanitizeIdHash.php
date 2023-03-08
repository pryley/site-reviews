<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

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
            require_once ABSPATH.WPINC.'/pluggable.php';
            $value = glsr()->prefix.substr(wp_hash(serialize($this->values), 'nonce'), -12, 8);
        }
        return $value;
    }

    protected function value(): string
    {
        return (new SanitizeId($this->value))->run();
    }
}
