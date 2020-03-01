<?php

namespace GeminiLabs\SiteReviews\Commands;

class RegisterWidgets
{
    public $widgets;

    public function __construct(array $input)
    {
        array_walk($input, function (&$args) {
            $args = wp_parse_args($args, [
                'description' => '',
                'name' => '',
            ]);
        });
        $this->widgets = $input;
    }
}
