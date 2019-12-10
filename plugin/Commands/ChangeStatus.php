<?php

namespace GeminiLabs\SiteReviews\Commands;

class ChangeStatus
{
    public $id;
    public $status;

    public function __construct($input)
    {
        $this->id = $input['post_id'];
        $this->status = 'approve' == $input['status']
            ? 'publish'
            : 'pending';
    }
}
