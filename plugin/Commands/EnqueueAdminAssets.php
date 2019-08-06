<?php

namespace GeminiLabs\SiteReviews\Commands;

class EnqueueAdminAssets
{
    public $pointers;

    public function __construct(array $input)
    {
        $this->pointers = $input['pointers'];
    }
}
