<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Helper;

class SettingBuilder extends Builder
{
    /**
     * @return void|string
     */
    public function buildFormElement()
    {
        $method = Helper::buildMethodName($this->tag, 'buildForm');
        return $this->$method().$this->buildAfter().$this->buildFieldDescription();
    }

    /**
     * @return string|void
     */
    protected function buildAfter()
    {
        if (!empty($this->args->after)) {
            return '&nbsp;'.$this->args->after;
        }
    }

    /**
     * @return string|void
     */
    protected function buildFieldDescription()
    {
        if (!empty($this->args->description)) {
            return $this->p([
                'class' => 'description',
                'text' => $this->args->description,
            ]);
        }
    }

    /**
     * @return string|void
     */
    protected function buildFormInputChoices()
    {
        $fields = [];
        $index = 0;
        foreach ($this->args->options as $value => $label) {
            $fields[] = $this->input([
                'checked' => in_array($value, $this->args->cast('value', 'array')),
                'id' => Helper::ifTrue(!empty($this->args->id), $this->args->id.'-'.++$index),
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

    /**
     * @return string|void
     */
    protected function buildFormTextarea()
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

    /**
     * @return array
     */
    protected function normalize(array $args, $type)
    {
        if (class_exists($className = $this->getFieldClassName($type))) {
            $args = $className::merge($args, 'setting');
        }
        return $args;
    }
}
