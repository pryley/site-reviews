<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Contracts\FieldContract;

class SettingBuilder extends Builder
{
    public function field(array $args): FieldContract
    {
        return new SettingField($args);
    }

    protected function buildFieldInputChoices(): string
    {
        $fields = [];
        $index = 0;
        foreach ($this->args()->options as $value => $label) {
            $fields[] = $this->input([
                'checked' => in_array($value, $this->args()->cast('value', 'array')),
                'disabled' => $this->args()->disabled,
                'id' => $this->indexedId(++$index),
                'label' => $label,
                'name' => $this->args()->name,
                'required' => $this->args()->required,
                'tabindex' => $this->args()->tabindex,
                'type' => $this->args()->type,
                'value' => $value,
            ]);
        }
        return $this->div([
            'class' => $this->args()->class,
            'text' => implode('<br>', $fields),
        ]);
    }
}
