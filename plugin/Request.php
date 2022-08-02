<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;

class Request extends Arguments
{
    /**
     * @param mixed $key
     * @param mixed $fallback
     * @return mixed
     */
    public function get($key, $fallback = null)
    {
        $value = Arr::get($this->getArrayCopy(), $key, null);
        if (is_null($fallback) || !Helper::isEmpty($value)) {
            return $value;
        }
        return Helper::runClosure($fallback);
    }

    /**
     * @return static
     * @todo support array values
     */
    public static function inputGet()
    {
        $values = Arr::consolidate(filter_input_array(INPUT_GET));
        foreach ($values as &$value) {
            if (!is_numeric($value)) {
                $value = sanitize_text_field(trim(Cast::toString($value)));
            }
        }
        return new static($values);
    }
}
