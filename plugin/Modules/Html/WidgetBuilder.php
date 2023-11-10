<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Helper;

class WidgetBuilder extends Builder
{
    public function buildFormElement(): string
    {
        $method = Helper::buildMethodName($this->tag, 'buildForm');
        return $this->$method().$this->buildFieldDescription();
    }

    protected function buildFieldDescription(): string
    {
        if (empty($this->args->description)) {
            return '';
        }
        return $this->small($this->args->description);
    }

    protected function buildFormInputChoices(): string
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
        return implode('<br>', $fields);
    }

    protected function normalize(array $args, string $type): array
    {
        if (class_exists($className = $this->getFieldClassName($type))) {
            $args = $className::merge($args, 'widget');
        }
        return $args;
    }
}
