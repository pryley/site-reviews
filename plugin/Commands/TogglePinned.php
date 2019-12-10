<?php

namespace GeminiLabs\SiteReviews\Commands;

class TogglePinned
{
    public $id;
    public $pinned;

    public function __construct($input)
    {
        $this->id = $input['id'];
        $this->pinned = isset($input['pinned'])
            ? wp_validate_boolean($input['pinned'])
            : null;
    }
}
