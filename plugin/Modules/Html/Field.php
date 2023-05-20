<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Defaults\FieldDefaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Style;

/**
 * @property string $id
 */
class Field
{
    /**
     * @var array
     */
    public $field;

    public function __construct(array $field = [])
    {
        $this->field = wp_parse_args($field, [
            'custom' => false,
            'errors' => false,
            'is_multi' => false,
            'is_raw' => false,
            'is_valid' => true,
            'path' => '',
            'raw_type' => '',
        ]);
        $this->normalize();
    }

    public function __get($key)
    {
        if (array_key_exists($key, $this->field)) {
            return $this->field[$key];
        }
    }

    public function __set($key, $value)
    {
        if (array_key_exists($key, $this->field)) {
            $this->field[$key] = $value;
        }
    }

    public function __toString()
    {
        return (string) $this->build();
    }

    /**
     * @return void|string
     */
    public function build()
    {
        if (!$this->field['is_valid']) {
            return;
        }
        if ($this->field['is_raw']) {
            return $this->builder()->{$this->field['type']}($this->field);
        }
        if ($this->field['is_multi']) {
            return $this->buildMultiField();
        }
        return $this->buildField();
    }

    /**
     * @return Builder
     */
    public function builder()
    {
        return glsr(Builder::class);
    }

    public function choiceType(): string
    {
        return Helper::ifTrue('toggle' === $this->field['raw_type'],
            $this->field['raw_type'],
            $this->field['type']
        );
    }

    public function fieldType(): string
    {
        $isChoice = in_array($this->field['raw_type'], ['checkbox', 'radio', 'toggle']);
        return Helper::ifTrue($isChoice, 'choice', $this->field['raw_type']);
    }

    public function getBaseClasses(string $key): array
    {
        return [
            glsr(Style::class)->classes($key),
            Str::suffix(glsr(Style::class)->defaultClasses($key), '-'.$this->fieldType()),
        ];
    }

    public function getField(): string
    {
        if ('choice' === $this->fieldType()) {
            return $this->buildFieldChoiceOptions();
        }
        return $this->builder()->raw($this->field);
    }

    public function getFieldClasses(): string
    {
        $classes = $this->getBaseClasses('field');
        if (!empty($this->field['errors'])) {
            $classes[] = glsr(Style::class)->validation('field_error');
        }
        if (!empty($this->field['required'])) {
            $classes[] = glsr(Style::class)->validation('field_required');
        }
        $classes = glsr()->filterArray('rendered/field/classes', $classes, $this->field);
        return implode(' ', $classes);
    }

    public function getFieldDescription(): string
    {
        if (empty($this->field['description'])) {
            return '';
        }
        return glsr(Template::class)->build('templates/form/field-description', [
            'context' => [
                'class' => implode(' ', $this->getBaseClasses('description')),
                'description' => $this->field['description'],
            ],
            'field' => $this->field,
        ]);
    }

    /**
     * @return string
     */
    public function getFieldErrors() // Extended by Review Filters
    {
        return glsr(Template::class)->build('templates/form/field-errors', [
            'context' => [
                'class' => glsr(Style::class)->validation('field_message'),
                'errors' => implode('<br>', Cast::toArray($this->field['errors'])), // because <br> is used in validation.js
            ],
            'field' => $this->field,
        ]);
    }

    public function getFieldLabel(): string
    {
        if (empty($this->field['label'])) {
            return '';
        }
        return $this->builder()->label([
            'class' => implode(' ', $this->getBaseClasses('label')),
            'for' => $this->field['id'],
            'text' => $this->builder()->span($this->field['label']),
        ]);
    }

    public function getFieldPrefix(): string
    {
        return glsr()->id;
    }

    public function render(): void
    {
        echo $this->build();
    }

    protected function buildField(): string
    {
        $field = glsr(Template::class)->build('templates/form/field_'.$this->field['raw_type'], [
            'context' => [
                'class' => $this->getFieldClasses(),
                'description' => $this->getFieldDescription(),
                'description_text' => $this->field['description'],
                'errors' => $this->getFieldErrors(),
                'field' => $this->getField(),
                'field_name' => $this->field['path'],
                'field_type' => $this->field['raw_type'],
                'for' => $this->field['id'],
                'label' => $this->getFieldLabel(),
                'label_text' => $this->field['label'],
            ],
            'field' => $this->field,
        ]);
        return glsr()->filterString('rendered/field', $field, $this->field['raw_type'], $this->field);
    }

    protected function buildFieldChoiceOptions(): string
    {
        $index = 0;
        return array_reduce(array_keys($this->field['options']), function ($carry, $value) use (&$index) {
            $args = glsr()->args($this->field);
            $type = $this->choiceType();
            $inputField = [
                'checked' => in_array($value, $args->cast('value', 'array')),
                'class' => $args->class,
                'id' => Helper::ifTrue(!empty($args->id), $args->id.'-'.++$index),
                'name' => $args->name,
                'required' => $args->required,
                'tabindex' => $args->tabindex,
                'type' => $args->type,
                'value' => $value,
            ];
            $html = glsr(Template::class)->build('templates/form/type-'.$type, [
                'context' => [
                    'class' => glsr(Style::class)->defaultClasses('field').'-'.$type, // only use the default class here!
                    'id' => $inputField['id'],
                    'input' => $this->builder()->raw($inputField),
                    'text' => $args->options[$value],
                ],
                'field' => $this->field,
                'input' => $inputField,
            ]);
            $html = glsr()->filterString('rendered/field', $html, $type, $inputField);
            return $carry.$html;
        });
    }

    protected function buildMultiField(): string
    {
        return $this->buildField();
    }

    protected function isFieldValid(): bool
    {
        $missingValues = [];
        $requiredValues = [
            'name', 'type',
        ];
        foreach ($requiredValues as $value) {
            if (!isset($this->field[$value])) {
                $missingValues[] = $value;
                $this->field['is_valid'] = false;
            }
        }
        if (!empty($missingValues)) {
            glsr_log()
                ->warning('Field is missing: '.implode(', ', $missingValues))
                ->debug($this->field);
        }
        return $this->field['is_valid'];
    }

    protected function mergeFieldArgs(string $className): array
    {
        return $className::merge($this->field);
    }

    protected function normalizeFieldArgs(): void
    {
        $className = Helper::buildClassName($this->field['type'], __NAMESPACE__.'\Fields');
        $className = glsr()->filterString('builder/field/'.$this->field['type'], $className);
        if (class_exists($className)) {
            $this->field = $this->mergeFieldArgs($className);
        }
    }

    protected function normalize(): void
    {
        if ($this->isFieldValid()) {
            $this->field['path'] = $this->field['name'];
            $this->field['raw_type'] = $this->field['type']; // save the original type before it's normalized
            $this->field = glsr(FieldDefaults::class)->merge($this->field);
            $this->normalizeFieldArgs();
            $this->normalizeFieldId();
            $this->normalizeFieldName();
            $this->field = glsr()->filterArray('field/'.$this->field['raw_type'], $this->field);
        }
    }

    /**
     * @return void
     */
    protected function normalizeFieldId() // Extended by Review Filters
    {
        if (!empty($this->field['id']) || $this->field['is_raw']) {
            return;
        }
        $this->field['id'] = Str::convertPathToId(
            $this->field['path'],
            $this->getFieldPrefix()
        );
    }

    /**
     * @return void
     */
    protected function normalizeFieldName() // Extended by Review Filters
    {
        $name = Str::convertPathToName($this->field['path'], $this->getFieldPrefix());
        if (count($this->field['options']) > 1 && 'checkbox' === $this->field['type']) {
            $name = Str::suffix($name, '[]'); // @todo is it necessary to do this both here and in the defaults?
        }
        $this->field['name'] = $name;
    }
}
