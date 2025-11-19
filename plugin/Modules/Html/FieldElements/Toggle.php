<?php

namespace GeminiLabs\SiteReviews\Modules\Html\FieldElements;

class Toggle extends Checkbox
{
    public function required(): array
    {
        return [
            'type' => 'checkbox',
        ];
    }

    protected function buildInput(array $args): string
    {
        $input = $this->field->builder()->input($args);
        $track = $this->field->builder()->span([
            'class' => 'glsr-toggle-track',
        ]);
        return $this->field->builder()->span([
            'class' => 'glsr-toggle',
            'text' => "{$input}{$track}",
        ]);
    }
}
