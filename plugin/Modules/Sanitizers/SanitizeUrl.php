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
        if (mb_strtolower($value) !== mb_strtolower($url)) {
            return '';
        }
        if (false === filter_var($url, FILTER_VALIDATE_URL)) {
            return '';
        }
        if (!empty($this->args[0]) && !$this->matchesHost($url)) {
            return '';
        }
        return $url;
    }

    protected function matchesHost(string $url): bool
    {
        $allowed = mb_strtolower(trim(preg_replace('#^https?://#i', '', (string) $this->args[0]), '/'));
        $host = mb_strtolower((string) wp_parse_url($url, PHP_URL_HOST));
        if ('' === $allowed || '' === $host) {
            return false;
        }
        return $host === $allowed || Str::endsWith($host, ['.'.$allowed]);
    }
}
