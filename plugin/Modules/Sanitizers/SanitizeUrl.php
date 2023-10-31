<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

use GeminiLabs\SiteReviews\Helpers\Str;

class SanitizeUrl extends StringSanitizer
{
    public function run(): string
    {
        $value = $this->value();
        if (!Str::startsWith($value, ['http://', 'https://'])) {
            $value = Str::prefix($value, 'https://');
        }
        $url = esc_url_raw($value);
        if (mb_strtolower($value) === mb_strtolower($url) && false !== filter_var($url, FILTER_VALIDATE_URL)) {
            return $url;
        }
        return '';
    }
}
