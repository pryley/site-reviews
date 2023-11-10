<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Fields;

class Checkbox extends Field
{
    public static function merge(array $args, string $fieldLocation = ''): array
    {
        $field = glsr()->args(parent::merge($args, $fieldLocation));
        $isChecked = $field->get('checked', false);
        $originalValue = $field->get('value', '');
        if (empty($field->options)) {
            $label = $field->get('label', $field->text);
            $value = $field->get('value', 1);
            $field->options = [$value => $label];
            $field->label = '';
            if (!$isChecked) {
                $field->value = '';
            }
        }
        if ('' === $originalValue && $isChecked) {
            $field->value = array_keys($field->options); // all options are checked
        }
        return $field->toArray();
    }

    public static function required(string $fieldLocation = ''): array
    {
        return [
            'is_multi' => true,
            'type' => 'checkbox',
        ];
    }

    public function tag(): string
    {
        return 'input';
    }
}
