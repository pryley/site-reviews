<?php

namespace GeminiLabs\SiteReviews\Commands;

class RegisterShortcodes
{
    public $shortcodes;

    public function __construct($input)
    {
        $this->shortcodes = $input;
    }
}
