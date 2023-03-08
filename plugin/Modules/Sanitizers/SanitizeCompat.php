<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;

class SanitizeCompat
{
    public $cast;
    public $value;
    public $values;

    public function __construct($value, string $cast, array $values = [])
    {
        $this->cast = $cast;
        $this->value = $value;
        $this->values = $values;
    }

    public function run()
    {
        if ('array' === $this->cast) {
            return Arr::consolidate($this->value);
        }
        return Cast::to($this->cast, $this->value);
    }
}
