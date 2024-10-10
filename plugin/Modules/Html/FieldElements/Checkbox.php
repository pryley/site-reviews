<?php

namespace GeminiLabs\SiteReviews\Modules\Html\FieldElements;

use GeminiLabs\SiteReviews\Arguments;
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

    public function inputType(): string
    {
        return in_array($this->field->original_type, ['checkbox', 'radio'])
            ? $this->field->type
            : $this->field->original_type;
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

    protected function buildInput(string $value, int $index, Arguments $args): string
    {
        $type = $this->inputType();
        $input = [
            'checked' => in_array($value, Cast::toArray($args->value)),
            'class' => $args->class,
            'disabled' => $args->disabled,
            'id' => Helper::ifTrue(!empty($args->id), "{$args->id}-{$index}"),
            'name' => $args->name,
            'required' => $args->required,
            'tabindex' => $args->tabindex,
            'type' => $args->type,
            'value' => $value,
        ];
        if (!empty($args['data-conditions'])) {
            $input['data-conditions'] = $args['data-conditions'];
        }
        $html = glsr(Template::class)->build("templates/form/type-{$type}", [
            'context' => [
                'class' => glsr(Style::class)->defaultClasses('field')."-{$type}", // only use the default class here!
                'id' => $input['id'],
                'input' => $this->field->builder()->input($input),
                'text' => $args->options[$value],
            ],
            'field' => $this->field,
            'input' => $input,
        ]);
        return glsr()->filterString('rendered/field', $html, $type, $input);
    }

    protected function buildReviewField(Arguments $args): string
    {
        $index = 0;
        $optionKeys = array_keys($args->options);
        return array_reduce($optionKeys, function ($carry, $value) use (&$index, $args) {
            return $carry.$this->buildInput((string) $value, ++$index, $args);
        }, '');
    }

    protected function normalizeOptions(): void
    {
        if (empty($this->field->options)) {
            $label = $this->field->label ?: $this->field->text;
            $value = $this->field->value ?: 1;
            $this->field->label = ''; // clear the label
            $this->field->text = ''; // clear the text
            $this->field->options = [$value => $label];
            return;
        }
        $options = [];
        foreach ($this->field->options as $key => $values) {
            if (!is_array($values)) {
                $options[$key] = $values;
                continue;
            }
            $values = array_slice(array_filter($values, 'is_string'), 0, 2);
            if (1 === count($values)) {
                $options[$key] = Cast::toString($values);
            } elseif (2 === count($values)) {
                $values = array_reduce($values, fn ($carry, $val) => $carry.$this->field->builder()->span($val), '');
                $options[$key] = $values;
            }
        }
        $this->field->options = $options;
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
