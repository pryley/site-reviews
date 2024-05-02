<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Sanitizer;
use GeminiLabs\SiteReviews\Modules\Style;

class ReviewField extends Field
{
    public function buildField(): string
    {
        if ($this->is_raw) {
            return $this->builder()->build($this->tag(), $this->toArray());
        }
        $field = glsr(Template::class)->build("templates/form/field_{$this->original_type}", [
            'context' => [
                'class' => $this->classAttrField(),
                'description' => $this->buildFieldDescription(),
                'errors' => $this->buildFieldErrors(),
                'field' => $this->buildFieldElement(),
                'field_name' => $this->original_name,
                'field_type' => $this->original_type,
                'label' => $this->buildFieldLabel(),
            ],
            'field' => $this,
        ]);
        return glsr()->filterString('rendered/field', $field, $this->original_type, $this);
    }

    public function buildFieldDescription(): string
    {
        if (empty($this->description)) {
            return '';
        }
        return glsr(Template::class)->build('templates/form/field-description', [
            'context' => [
                'class' => $this->classAttrDescription(),
                'description' => $this->description,
            ],
            'field' => $this,
        ]);
    }

    public function buildFieldElement(): string
    {
        return $this->fieldElement()->build([
            'class' => $this->classAttrElement(), // merge the styled class attribute
            'label' => '', // prevent the field label from being built
        ]);
    }

    public function buildFieldErrors(): string
    {
        return glsr(Template::class)->build('templates/form/field-errors', [
            'context' => [
                'class' => $this->classAttrErrors(),
                'errors' => implode('<br>', Arr::consolidate($this->errors)), // because <br> is used in validation.js
            ],
            'field' => $this,
        ]);
    }

    public function buildFieldLabel(): string
    {
        if (empty($this->label)) {
            return '';
        }
        return glsr(Template::class)->build('templates/form/field-label', [
            'context' => [
                'class' => $this->classAttrLabel(),
                'for' => !$this->isChoiceField() ? $this->id : '',
                'text' => $this->label,
            ],
            'field' => $this,
        ]);
    }

    public function location(): string
    {
        return 'review';
    }

    protected function classAttrDescription(): string
    {
        $classes = [
            glsr(Style::class)->classes('description'),
            glsr(Style::class)->defaultClasses('description'),
        ];
        $classes = implode(' ', $classes);
        return glsr(Sanitizer::class)->sanitizeAttrClass($classes);
    }

    protected function classAttrElement(): string
    {
        $classes = [
            glsr(Style::class)->fieldClass($this),
        ];
        if ('yes_no' === $this->original_type) {
            $classes[] = "glsr-input-{$this->type}";
        } elseif ($this->isChoiceField()) {
            $classes[] = "glsr-input-{$this->original_type}";
        } elseif (glsr(Attributes::class)->isInputType($this->type)) {
            $classes[] = "glsr-input glsr-input-{$this->type}";
        } else {
            $classes[] = "glsr-{$this->tag()}";
        }
        $classes = implode(' ', $classes);
        return glsr(Sanitizer::class)->sanitizeAttrClass($classes);
    }

    protected function classAttrErrors(): string
    {
        return glsr(Sanitizer::class)->sanitizeAttrClass(
            glsr(Style::class)->validation('field_message')
        );
    }

    protected function classAttrField(): string
    {
        $suffix = $this->isChoiceField() ? '-choice' : "-{$this->original_type}";
        $classes = [
            glsr(Style::class)->classes('field'),
            glsr(Style::class)->defaultClasses('field'),
            Str::suffix(glsr(Style::class)->defaultClasses('field'), $suffix),
        ];
        if (!empty($this->errors)) {
            $classes[] = glsr(Style::class)->validation('field_error');
        }
        if ($this->is_hidden) {
            $classes[] = glsr(Style::class)->validation('field_hidden');
        }
        if (!empty($this->required)) {
            $classes[] = glsr(Style::class)->validation('field_required');
        }
        $classes = implode(' ', $classes);
        $classes = explode(' ', $classes);
        $classes = array_values(array_filter(array_unique($classes)));
        $classes = glsr()->filterArrayUnique('rendered/field/classes', $classes, $this);
        $classes = implode(' ', $classes);
        $classes = glsr(Sanitizer::class)->sanitizeAttrClass($classes);
        return $classes;
    }

    protected function classAttrLabel(): string
    {
        $classes = [
            glsr(Style::class)->classes('label'),
            glsr(Style::class)->defaultClasses('label'),
        ];
        $classes = implode(' ', $classes);
        return glsr(Sanitizer::class)->sanitizeAttrClass($classes);
    }

    protected function normalize(): void
    {
        parent::normalize();
        $this->normalizeDataConditions();
        $this->normalizeRequired(); // do this before normalizing validation
        $this->normalizeValidation();
    }

    protected function normalizeDataConditions(): void
    {
        $conditions = wp_parse_args($this->conditions(), [
            'criteria' => '',
            'conditions' => [],
        ]);
        if (in_array($conditions['criteria'], ['', 'always'])) {
            return;
        }
        if (empty($conditions['conditions'])) {
            return;
        }
        $this['data-conditions'] = wp_json_encode($conditions);
    }

    protected function normalizeRequired(): void
    {
        if ($this->is_custom) {
            return; // don't modify the required attribute in custom fields
        }
        if (!in_array($this->original_name, glsr_get_option('forms.required', [], 'array'))) {
            return;
        }
        $this->required = true;
    }

    protected function normalizeValidation(): void
    {
        $rules = explode('|', $this->validation);
        $rules = array_filter(array_diff($rules, ['accepted', 'required']));
        if ($this->required) {
            $rule = 'terms' === $this->original_name ? 'accepted' : 'required';
            array_unshift($rules, $rule);
        }
        $this->validation = implode('|', $rules);
    }
}
