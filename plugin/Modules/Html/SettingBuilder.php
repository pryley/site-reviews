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

    protected function buildFieldTextareaElement(): string
    {
        $element = parent::buildFieldTextareaElement();
        if (empty($this->args()->tags)) {
            return $element;
        }
        $tags = array_keys($this->args()->tags);
        $buttons = array_reduce($tags, fn ($carry, $tag) => $carry.$this->input([
            'class' => 'button button-small',
            'data-tag' => esc_attr($tag),
            'type' => 'button',
            'value' => esc_attr($this->args()->tags[$tag]),
        ]), '');
        $toolbar = $this->div([
            'class' => 'quicktags-toolbar',
            'text' => $buttons,
        ]);
        return $this->div([
            'class' => 'glsr-template-editor',
            'text' => $element.$toolbar,
        ]);
    }
}
