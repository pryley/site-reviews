<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Cast;

class SettingBuilder extends Builder
{
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
