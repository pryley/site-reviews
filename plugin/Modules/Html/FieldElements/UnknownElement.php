<?php

namespace GeminiLabs\SiteReviews\Modules\Html\FieldElements;

use GeminiLabs\SiteReviews\Modules\Html\Attributes;

class UnknownElement extends AbstractFieldElement
{
    public function build(array $overrideArgs = []): string
    {
        if (empty($this->tag())) {
            return '';
        }
        return parent::build($overrideArgs);
    }

    public function merge(): void
    {
        if (empty($this->tag())) {
            $this->field->is_valid = false;
            return;
        }
        parent::merge();
    }

    public function tag(): string
    {
        if (in_array($this->field->type, Attributes::INPUT_TYPES)) {
            return 'input';
        }
        return '';
    }
}
