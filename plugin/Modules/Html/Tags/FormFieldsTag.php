<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Html\Field;
use GeminiLabs\SiteReviews\Modules\Html\Form;

class FormFieldsTag extends FormTag
{
    /**
     * @return array
     */
    protected function fields()
    {
        $hiddenFields = $this->hiddenFields();
        $hiddenFields[] = $this->honeypotField();
        $fields = $this->normalizeFields(glsr(Form::class)->getFields('submission-form'));
        $paths = array_map(function ($obj) {
            return $obj->field['path'];
        }, $hiddenFields);
        foreach ($fields as $field) {
            $index = array_search($field->field['path'], $paths);
            if (false === $index) {
                continue;
            }
            unset($hiddenFields[$index]);
        }
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
        $fields = [[
            'name' => '_action',
            'value' => 'submit-review',
        ], [
            'name' => '_counter',
        ], [
            'name' => '_nonce',
            'value' => wp_create_nonce('submit-review'),
        ], [
            'name' => '_post_id',
            'value' => get_the_ID(),
        ], [
            'name' => '_referer',
            'value' => wp_unslash(filter_input(INPUT_SERVER, 'REQUEST_URI')),
        ], [
            'name' => 'assign_to',
            'value' => $this->args->assign_to,
        ], [
            'name' => 'category',
            'value' => $this->args->category,
        ], [
            'name' => 'excluded',
            'value' => $this->args->hide,
        ], [
            'name' => 'form_id',
            'value' => $this->args->id,
        ]];
        return array_map(function ($field) {
            return new Field(wp_parse_args($field, ['type' => 'hidden']));
        }, $fields);
    }

    /**
     * @return Field
     */
    protected function honeypotField()
    {
        return new Field([
            'name' => 'honeypot',
            'suffix' => $this->args->id, // @hack
            'type' => 'honeypot',
        ]);
    }

    /**
     * @return void
     */
    protected function normalizeFieldClass(Field &$field)
    {
        $field->field['class'] = trim(Arr::get($field->field, 'class').' glsr-field-control');
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
            if (in_array($field->field['path'], $this->args->hide)) {
                continue;
            }
            $field->field['is_public'] = true;
            $this->normalizeFieldClass($field);
            $this->normalizeFieldErrors($field);
            $this->normalizeFieldRequired($field);
            $this->normalizeFieldValue($field);
            $this->normalizeFieldId($field);
            $normalizedFields[] = $field;
        }
        return $normalizedFields;
    }

    /**
     * @return void
     */
    protected function normalizeFieldValue(Field &$field)
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
