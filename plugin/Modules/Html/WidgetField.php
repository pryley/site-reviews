<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Contracts\BuilderContract;

class WidgetField extends Field
{
    public function builder(): BuilderContract
    {
        return glsr(WidgetBuilder::class);
    }

    public function buildField(): string
    {
        if ($this->is_raw) { // only build the field element
            return $this->builder()->build($this->tag(), $this->toArray());
        }
        if ('number' === $this->original_type) {
            return $this->builder()->p([
                'style' => 'display:flex;align-items:baseline;gap:5px;',
                'text' => $this->buildFieldElement().$this->buildFieldLabel(),
            ]);
        }
        return $this->builder()->p([
            'text' => $this->buildFieldLabel().$this->buildFieldElement(),
        ]);
    }

    public function location(): string
    {
        return 'widget';
    }

    public function namePrefix(): string
    {
        return '';
    }
}
