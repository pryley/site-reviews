<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

use GeminiLabs\SiteReviews\Helpers\Arr;

class SanitizeTextHtml extends StringSanitizer
{
    public function run(): string
    {
        $allowedHtmlPost = wp_kses_allowed_html('post');
        $allowedHtml = [
            'a' => Arr::get($allowedHtmlPost, 'a'),
            'em' => Arr::get($allowedHtmlPost, 'em'),
            'strong' => Arr::get($allowedHtmlPost, 'strong'),
        ];
        $allowedHtml = glsr()->filterArray('sanitize/allowed-html', $allowedHtml, $this);
        return wp_kses($this->value(), $allowedHtml);
    }
}
