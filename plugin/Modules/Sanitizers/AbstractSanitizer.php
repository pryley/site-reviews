<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

abstract class AbstractSanitizer
{
    public array $args;
    public $value;
    public array $values;

    public function __construct($value, array $args = [], array $values = [])
    {
        $args = array_pad($args, 2, ''); // minimum of 2 args
        $this->args = $args;
        $this->value = $value;
        $this->values = $values;
    }
}
