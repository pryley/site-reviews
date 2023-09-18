<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

abstract class AbstractSanitizer
{
    public $args;
    public $value;
    public $values;

    public function __construct($value, array $args = [], array $values = [])
    {
        $args = array_pad($args, 2, ''); // minimum of 2 args
        $this->args = array_splice($args, 0, 2); // maximum of 2 args
        $this->value = $value;
        $this->values = $values;
    }
}
