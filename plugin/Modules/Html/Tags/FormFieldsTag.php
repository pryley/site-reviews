<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Honeypot;
use GeminiLabs\SiteReviews\Modules\Html\Attributes;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\Field;
use GeminiLabs\SiteReviews\Modules\Html\Form;

class FormFieldsTag extends FormTag
{
    /**
     * @return array
     */
    protected function fields()
    {
        $fields = glsr(Form::class)->getFields('review-form');
        $fields = $this->normalizeFields($fields);
        $hiddenFields = $this->hiddenFields();
        $paths = wp_list_pluck(wp_list_pluck($hiddenFields, 'field'), 'path');
        foreach ($fields as $field) {
            $index = array_search($field->field['path'], $paths);
            if (false !== $index) {
                unset($hiddenFields[$index]);
            }
        }
        array_unshift($fields, glsr(Honeypot::class)->build($this->args->id));
        return array_merge($hiddenFields, $fields);
    }

    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        return array_reduce($this->fields(), function ($carry, $field) {
            return $carry.$field;
        });
    }

    /**
     * @return array
     */
    protected function hiddenFields()
    {
        $fields = [];
        $referer = filter_input(INPUT_SERVER, 'REQUEST_URI');
        $referer = glsr()->filterString('review-form/referer', $referer);
        $hiddenFields = [
            '_action' => 'submit-review',
            '_counter' => null,
            '_nonce' => wp_create_nonce('submit-review'),
            '_post_id' => get_the_ID(),
            '_referer' => wp_unslash($referer),
            'assigned_posts' => $this->args->assigned_posts,
            'assigned_terms' => $this->args->assigned_terms,
            'assigned_users' => $this->args->assigned_users,
            'excluded' => $this->args->hide,
            'form_id' => $this->args->id,
        ];
        foreach ($hiddenFields as $name => $value) {
            $fields[] = new Field([
                'name' => $name,
                'type' => 'hidden',
                'value' => $value,
            ]);
        }
        return $fields;
    }

    /**
     * @return void
     */
    protected function normalizeFieldClasses(Field &$field)
    {
        $classes = [];
        $isInput = in_array($field->field['type'], Attributes::INPUT_TYPES);
        $isChoice = 'choice' === $field->fieldType();
        if ($isInput) {
            if (!$isChoice) {
                $classes[] = 'glsr-input';
            }
            $classes[] = 'glsr-input-'.$field->choiceType();
        } else {
            $classes[] = 'glsr-'.$field->field['type'];
        }
        $classes[] = trim(Arr::get($field->field, 'class'));
        $field->field['class'] = implode(' ', $classes);
    }

    /**
     * @return void
     */
    protected function normalizeFieldId(Field &$field)
    {
        if (!empty($this->args->id) && !empty($field->field['id'])) {
            $field->field['id'] .= '-'.$this->args->id;
        }
    }

    /**
     * @return void
     */
    protected function normalizeFieldErrors(Field &$field)
    {
        if (array_key_exists($field->field['path'], $this->with->errors)) {
            $field->field['errors'] = $this->with->errors[$field->field['path']];
        }
    }

    /**
     * @return void
     */
    protected function normalizeFieldRequired(Field &$field)
    {
        if (in_array($field->field['path'], $this->with->required)) {
            $field->field['required'] = true;
        }
    }

    /**
     * @return array
     */
    protected function normalizeFields($fields)
    {
        $normalizedFields = [];
        foreach ($fields as $field) {
            if (!in_array($field->field['path'], $this->args->hide)) {
                $this->normalizeFieldClasses($field);
                $this->normalizeFieldErrors($field);
                $this->normalizeFieldRequired($field);
                $this->normalizeFieldValue($field);
                $this->normalizeFieldId($field);
                $normalizedFields[] = $field;
            }
        }
        return $normalizedFields;
    }

    /**
     * @return void
     */
    protected function normalizeFieldValue(Field $field)
    {
        if (!array_key_exists($field->field['path'], $this->with->values)) {
            return;
        }
        if (in_array($field->field['type'], ['radio', 'checkbox'])) {
            $field->field['checked'] = $field->field['value'] == $this->with->values[$field->field['path']];
        } else {
            $field->field['value'] = $this->with->values[$field->field['path']];
        }
    }
}
