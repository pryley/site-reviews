<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

use GeminiLabs\SiteReviews\Helpers\Cast;

abstract class StringSanitizer extends AbstractSanitizer
{
    abstract public function run(): string;

    protected function kses(string $value): string
    {
        $regex = '/on(abort|autocomplete|autocompleteerror|beforeprint|beforeunload|blur|cancel|canplay|canplaythrough|change|click|close|contextmenu|cuechange|dblclick|drag|dragend|dragenter|dragleave|dragover|dragstart|drop|durationchange|emptied|ended|error|focus|hashchange|input|invalid|keydown|keypress|keyup|languagechange|load|loadeddata|loadedmetadata|loadstart|message|mousedown|mouseenter|mouseleave|mousemove|mouseout|mouseover|mouseup|mousewheel|offline|online|pause|play|playing|popstate|progress|ratechange|redo|reset|resize|scroll|seeked|seeking|select|show|sort|stalled|storage|submit|suspend|timeupdate|toggle|undo|unload|volumechange|waiting)\s*=/i';
        $value = preg_replace($regex, '', $value); // remove all event function attributes
        $value = preg_replace('/(;amp)+/i', ';amp', $value);
        $value = str_replace('&amp;amp;', '&amp;', $value);
        return $value;
    }

    protected function value(): string
    {
        return trim(Cast::toString($this->value));
    }
}
