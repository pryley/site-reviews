<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Honeypot;

class FormFields
{
    public Arguments $args;
    public Arguments $with;

    public function __construct(array $args, Arguments $with)
    {
        $this->args = glsr()->args($args);
        $this->with = $with;
    }

    public function form(): Form
    {
        $visibleFields = $this->visibleFields();
        $hiddenFields = array_merge($this->hiddenFields(), [
            'honeypot' => glsr(Honeypot::class)->build($this->args->form_id),
        ]);
        foreach ($visibleFields as $name => $field) {
            unset($hiddenFields[$name]);
        }
        return new Form($visibleFields, $hiddenFields);
    }

    public function hiddenFields(): array
    {
        $fields = [];
        $referer = filter_input(INPUT_SERVER, 'REQUEST_URI');
        $referer = glsr()->filterString('review-form/referer', $referer);
        do_action('litespeed_nonce', 'submit-review'); // @litespeedcache
        $hiddenFields = [
            '_action' => 'submit-review',
            '_nonce' => wp_create_nonce('submit-review'),
            '_post_id' => get_the_ID(),
            '_referer' => wp_unslash($referer),
            'assigned_posts' => $this->args->assigned_posts,
            'assigned_terms' => $this->args->assigned_terms,
            'assigned_users' => $this->args->assigned_users,
            'excluded' => $this->args->hide,
            'form_id' => $this->args->form_id,
            'terms_exist' => Cast::toInt(!in_array('terms', $this->args->hide)),
        ];
        foreach ($hiddenFields as $name => $value) {
            $fields[$name] = new Field([
                'name' => $name,
                'type' => 'hidden',
                'value' => $value,
            ]);
        }
        return glsr()->filterArray('review-form/fields/hidden', $fields, $this->args);
    }

    public function visibleFields(): array
    {
        $fields = glsr()->config('forms/review-form');
        $fields = glsr()->filterArray('review-form/fields', $fields, $this->args);
        foreach ($fields as $name => &$field) {
            $field = new Field(wp_parse_args($field, ['name' => $name]));
        }
        return $this->normalize($fields);
    }

    public function normalize($fields): array
    {
        $normalizedFields = [];
        foreach ($fields as $name => $field) {
            if (!in_array($field->field['path'], $this->args->hide)) {
                $this->normalizeFieldClasses($field);
                $this->normalizeFieldErrors($field);
                $this->normalizeFieldRequired($field);
                $this->normalizeFieldValue($field);
                $this->normalizeFieldId($field);
                $normalizedFields[$name] = $field;
            }
        }
        return glsr()->filterArray('review-form/fields/normalized', $normalizedFields, $this->args);
    }

    protected function normalizeFieldClasses(Field $field): void
    {
        if ('hidden' === $field->fieldType()) {
            return;
        }
        $fieldClasses = [
            'input' => ['glsr-input', 'glsr-input-'.$field->choiceType()],
            'choice' => ['glsr-input-'.$field->choiceType()],
            'other' => ['glsr-'.$field->field['type']],
        ];
        if ('choice' === $field->fieldType()) {
            $classes = $fieldClasses['choice'];
        } elseif (in_array($field->field['type'], Attributes::INPUT_TYPES)) {
            $classes = $fieldClasses['input'];
        } else {
            $classes = $fieldClasses['other'];
        }
        $classes[] = trim(Arr::get($field->field, 'class'));
        $field->field['class'] = implode(' ', $classes);
    }

    protected function normalizeFieldId(Field $field): void
    {
        if (!empty($this->args->id) && !empty($field->field['id'])) {
            $field->field['id'] .= '-'.$this->args->id;
        }
    }

    protected function normalizeFieldErrors(Field $field): void
    {
        if (array_key_exists($field->field['path'], $this->with->errors)) {
            $field->field['errors'] = $this->with->errors[$field->field['path']];
        }
    }

    protected function normalizeFieldRequired(Field $field): void
    {
        if (!$field->field['custom'] // do not change custom fields
            && in_array($field->field['path'], $this->with->required)) {
            $field->field['required'] = true;
        }
    }

    protected function normalizeFieldValue(Field $field): void
    {
        if (!array_key_exists($field->field['path'], $this->with->values)) {
            return;
        }
        if (in_array($field->field['type'], ['radio', 'checkbox'])) {
            $isChecked = $field->field['value'] == $this->with->values[$field->field['path']];
            $field->field['checked'] = $isChecked;
        } else {
            $field->field['value'] = $this->with->values[$field->field['path']];
        }
    }
}
