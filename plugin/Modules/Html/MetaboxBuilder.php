<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Contracts\FieldContract;

class MetaboxBuilder extends Builder
{
    public function field(array $args): FieldContract
    {
        return new MetaboxField($args);
    }
}
