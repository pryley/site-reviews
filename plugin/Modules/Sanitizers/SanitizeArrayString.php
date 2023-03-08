<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

class SanitizeArrayString extends ArraySanitizer
{
    public function run(): array
    {
        $values = array_filter($this->value(), 'is_string');
        foreach ($values as $key => $value) {
            $values[$key] = (new SanitizeText($value))->run();
        }
        return $values;
    }
}
