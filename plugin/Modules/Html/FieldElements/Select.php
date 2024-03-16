<?php

namespace GeminiLabs\SiteReviews\Modules\Html\FieldElements;

use GeminiLabs\SiteReviews\Helpers\Cast;

class Select extends AbstractFieldElement
{
    public function defaults(): array
    {
        $locations = [
            'widget' => 'widefat',
        ];
        return array_filter([
            'class' => $locations[$this->field->location()] ?? '',
        ]);
    }

    protected function normalizeValue(): void
    {
        if (!$this->field->isMultiField()) {
            return;
        }
        if ('' === $this->field->value && $this->field->selected) {
            $this->field->value = array_keys($this->field->options); // all options are selected
            return;
        }
        $this->field->value = Cast::toArray($this->field->value); // cast value to array as the field accepts multiple values
    }
}
