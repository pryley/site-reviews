<?php

namespace GeminiLabs\SiteReviews\Modules\Html\FieldElements;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Modules\Style;

class Range extends Checkbox
{
    public function required(): array
    {
        return [
            'class' => 'glsr-input-range',
            'type' => 'radio',
        ];
    }

    protected function buildReviewField(): string
    {
        $index = 0;
        $optionKeys = array_keys($this->field->options);
        $field = array_reduce($optionKeys, function ($carry, $value) use (&$index) {
            $type = $this->field->original_type;
            $inputField = [
                'checked' => in_array($value, Cast::toArray($this->field->value)),
                'class' => $this->field->class,
                'disabled' => $this->field->disabled,
                'id' => Helper::ifTrue(!empty($this->field->id), $this->field->id.'-'.++$index),
                'name' => $this->field->name,
                'required' => $this->field->required,
                'tabindex' => $this->field->tabindex,
                'type' => $this->field->type,
                'value' => $value,
            ];
            if (!empty($this->field['data-conditions'])) {
                $inputField['data-conditions'] = $this->field['data-conditions'];
            }
            $html = glsr(Template::class)->build("templates/form/type-{$type}", [
                'context' => [
                    'class' => glsr(Style::class)->defaultClasses('field')."-{$type}-input", // only use the default class here!
                    'id' => $inputField['id'],
                    'input' => $this->field->builder()->input($inputField),
                    'text' => $this->field->options[$value],
                ],
                'field' => $this->field,
                'input' => $inputField,
            ]);
            $html = glsr()->filterString('rendered/field', $html, $type, $inputField);
            return $carry.$html;
        }, '');
        $field = glsr(Builder::class)->div([
            'class' => 'glsr-field-range',
            'data-placeholder' => __('Please select', 'site-reviews'),
            'text' => $field,
        ]);
        if (!empty($this->field->labels)) {
            $labels = [
                Arr::get($this->field->labels, 0, ''),
                Arr::get($this->field->labels, 1, ''),
                Arr::get($this->field->labels, 2, ''),
            ];
            $labels = array_reduce($labels,
                fn ($carry, $label) => $carry.glsr(Builder::class)->span($label),
            '');
            $labels = glsr(Builder::class)->div([
                'class' => 'glsr-range-labels',
                'text' => $labels,
            ]);
            $field = $labels.$field;
        }
        return $field;
    }
}
