<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Contracts\FieldContract;
use GeminiLabs\SiteReviews\Helper;

class WidgetBuilder extends Builder
{
    public function field(array $args): FieldContract
    {
        return new WidgetField($args);
    }

    protected function buildFieldDescription(): string
    {
        if (empty($this->args()->description)) {
            return '';
        }
        return $this->small($this->args()->description);
    }

    protected function buildFieldElement(): string
    {
        $method = Helper::buildMethodName('build', 'field', $this->tag(), 'element');
        $element = call_user_func([$this, $method]);
        return $element.$this->buildFieldDescription();
    }

    protected function buildFieldInputChoices(): string
    {
        $fields = [];
        $index = 0;
        foreach ($this->args()->options as $value => $label) {
            $fields[] = $this->input([
                'checked' => in_array($value, $this->args()->cast('value', 'array')),
                'id' => $this->indexedId(++$index),
                'label' => $label,
                'name' => $this->args()->name,
                'type' => $this->args()->type,
                'value' => $value,
            ]);
        }
        return implode('<br>', $fields);
    }
}
