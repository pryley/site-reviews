<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

use GeminiLabs\SiteReviews\Helpers\Cast;

abstract class StringSanitizer extends AbstractSanitizer
{
    abstract public function run(): string;

    protected function kses(string $value): string
    {
        $regex = '/on(beforeprint|beforeunload|blur|error|focus|hashchange|languagechange|load|message|offline|online|popstate|redo|resize|storage|undo|unload)\s*=/i';
        $value = preg_replace($regex, '', $value); // remove all function attributes
        return $value;
    }

    protected function value(): string
    {
        return trim(Cast::toString($this->value));
    }
}
