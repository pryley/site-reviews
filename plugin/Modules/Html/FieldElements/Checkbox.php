<?php

namespace GeminiLabs\SiteReviews\Modules\Html\FieldElements;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Modules\Style;

class Checkbox extends AbstractFieldElement
{
    public function defaults(): array
    {
        return [
            'checked' => false,
        ];
    }

    public function required(): array
    {
        return [
            'type' => 'checkbox',
        ];
    }

    public function tag(): string
    {
        return 'input';
    }

    protected function buildReviewField(): string
    {
        $index = 0;
        $optionKeys = array_keys($this->field->options);
        $type = 'toggle' === $this->field->original_type ? $this->field->original_type : $this->field->type;
        return array_reduce($optionKeys, function ($carry, $value) use (&$index, $type) {
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
            $html = glsr(Template::class)->build("templates/form/type-{$type}", [
                'context' => [
                    'class' => glsr(Style::class)->defaultClasses('field')."-{$type}", // only use the default class here!
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
    }

    protected function normalizeOptions(): void
    {
        if (!empty($this->field->options)) {
            return;
        }
        $label = $this->field->label ?: $this->field->text;
        $value = $this->field->value ?: 1;
        $this->field->label = ''; // clear the label
        $this->field->text = ''; // clear the text
        $this->field->options = [$value => $label];
    }

    protected function normalizeValue(): void
    {
        if (!$this->field->isMultiField()) {
            return;
        }
        if ('' === $this->field->value && $this->field->checked) {
            $this->field->value = array_keys($this->field->options); // all options are checked
            return;
        }
        $this->field->value = Cast::toArray($this->field->value); // cast value to array as the field accepts multiple values
    }
}
