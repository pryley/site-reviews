<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Contracts\BuilderContract;

class WidgetField extends Field
{
    public function builder(): BuilderContract
    {
        return glsr(WidgetBuilder::class);
    }

    public function location(): string
    {
        return 'widget';
    }
}
