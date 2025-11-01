<?php

namespace GeminiLabs\SiteReviews\Modules\Html\FieldElements;

use GeminiLabs\SiteReviews\Arguments;

class Code extends AbstractFieldElement
{
    public function defaults(): array
    {
        $locations = [
            'setting' => 'large-text code',
        ];
        return array_filter([
            'class' => $locations[$this->field->location()] ?? '',
        ]);
    }

    public function required(): array
    {
        $locations = [
            'setting' => $this->field->autosize ? 'autosized' : '',
        ];
        return [
            'class' => $locations[$this->field->location()] ?? '',
            'type' => 'textarea',
        ];
    }

    public function tag(): string
    {
        return 'textarea';
    }

    protected function buildSettingField(Arguments $args): string
    {
        $textarea = $this->field->builder()->build($this->tag(), $args->toArray());
        if (!empty($args->tags)) {
            $quicktags = $this->quicktags($args);
            $textarea = $this->field->builder()->div([
                'class' => 'glsr-template-editor',
                'text' => $textarea.$quicktags,
            ]);
        }
        return $textarea;
    }

    protected function quicktags(Arguments $args): string
    {
        $tags = array_keys($args->tags);
        $buttons = array_reduce($tags, fn ($carry, $tag) => $carry.$this->field->builder()->input([
            'class' => 'button button-small',
            'data-tag' => esc_attr($tag),
            'type' => 'button',
            'value' => esc_attr($args->tags[$tag]),
        ]), '');
        return $this->field->builder()->div([
            'class' => 'quicktags-toolbar',
            'text' => $buttons,
        ]);
    }
}
