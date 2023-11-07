<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Arguments;

class FormTag extends Tag
{
    /**
     * @param mixed $with
     */
    protected function validate($with): bool
    {
        return $with instanceof Arguments;
    }
}
