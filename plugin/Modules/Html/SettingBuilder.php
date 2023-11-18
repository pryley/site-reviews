<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Helper;

class SettingBuilder extends Builder
{
    public function buildFormElement(): string
    {
        $method = Helper::buildMethodName($this->tag, 'buildForm');
        return $this->$method().$this->buildAfter().$this->buildFieldDescription();
    }

    protected function buildAfter(): string
    {
        if (empty($this->args->after)) {
            return '';
        }
        return "&nbsp;{$this->args->after}";
    }

    protected function buildFieldDescription(): string
    {
        if (empty($this->args->description)) {
            return '';
        }
        return $this->p([
            'class' => 'description',
            'text' => $this->args->description,
        ]);
    }

    protected function buildFormInputChoices(): string
    {
        $fields = [];
        $index = 0;
        foreach ($this->args->options as $value => $label) {
            $fields[] = $this->input([
                'checked' => in_array($value, $this->args->cast('value', 'array')),
                'id' => Helper::ifTrue(!empty($this->args->id), "{$this->args->id}-".++$index),
                'label' => $label,
                'name' => $this->args->name,
                'type' => $this->args->type,
                'value' => $value,
            ]);
        }
        return $this->div([
            'class' => $this->args->class,
            'text' => implode('<br>', $fields),
        ]);
    }

    protected function buildFormTextarea(): string
    {
        $textarea = $this->buildFormLabel().$this->buildDefaultElement(
            esc_html($this->args->cast('value', 'string'))
        );
        if (empty($this->args->tags)) {
            return $textarea;
        }
        $buttons = [];
        foreach ($this->args->tags as $tag => $label) {
            $buttons[] = $this->input([
                'class' => 'button button-small',
                'data-tag' => $tag,
                'type' => 'button',
                'value' => $label,
            ]);
        }
        $toolbar = $this->div([
            'class' => 'quicktags-toolbar',
            'text' => implode('', $buttons),
        ]);
        return $this->div([
            'class' => 'glsr-template-editor',
            'text' => $textarea.$toolbar,
        ]);
    }

    protected function normalize(array $args, string $type): array
    {
        if (class_exists($className = $this->getFieldClassName($type))) {
            $args = $className::merge($args, 'setting');
        }
        return $args;
    }
}
