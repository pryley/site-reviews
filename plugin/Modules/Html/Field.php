<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\Attributes;

class Field
{
    /**
     * @var array
     */
    public $field;

    public function __construct(array $field = [])
    {
        $this->field = wp_parse_args($field, [
            'errors' => false,
            'is_hidden' => false,
            'is_multi' => false,
            'is_raw' => false,
            'is_valid' => true,
            'path' => '',
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

    /**
     * @return string
     */
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

    /**
     * @return string
     */
    public function getField()
    {
        return $this->builder()->raw($this->field);
    }

    /**
     * @return string
     */
    public function getFieldClass()
    {
        $classes = [];
        if (!empty($this->field['errors'])) {
            $classes[] = 'glsr-has-error';
        }
        if ($this->field['is_hidden']) {
            $classes[] = 'hidden';
        }
        if (!empty($this->field['required'])) {
            $classes[] = 'glsr-required';
        }
        $classes = glsr()->filterArray('rendered/field/classes', $classes, $this->field);
        return implode(' ', $classes);
    }

    /**
     * @return string
     */
    public function getFieldDependsOn()
    {
        return !empty($this->field['data-depends'])
            ? $this->field['data-depends']
            : '';
    }

    /**
     * @return void|string
     */
    public function getFieldErrors()
    {
        if (empty($this->field['errors']) || !is_array($this->field['errors'])) {
            return;
        }
        $errors = array_reduce($this->field['errors'], function ($carry, $error) {
            return $carry.$this->builder()->span($error, ['class' => 'glsr-field-error']);
        });
        return glsr(Template::class)->build('templates/form/field-errors', [
            'context' => [
                'errors' => $errors,
            ],
            'field' => $this->field,
        ]);
    }

    /**
     * @return string
     */
    public function getFieldLabel()
    {
        if (!empty($this->field['label'])) {
            return $this->builder()->label([
                'class' => 'glsr-'.$this->fieldType().'-label',
                'for' => $this->field['id'],
                'text' => $this->builder()->span($this->field['label']),
            ]);
        }
    }

    /**
     * @return string
     */
    public function getFieldPrefix()
    {
        return glsr()->id;
    }

    /**
     * @return void
     */
    public function render()
    {
        echo $this->build();
    }

    /**
     * @return string
     */
    protected function buildField()
    {
        $field = glsr(Template::class)->build('templates/form/field_'.$this->field['type'], [
            'context' => [
                'class' => $this->getFieldClass(),
                'errors' => $this->getFieldErrors(),
                'field' => $this->getField(),
                'label' => $this->getFieldLabel(),
            ],
            'field' => $this->field,
        ]);
        return glsr()->filterString('rendered/field', $field, $this->field['type'], $this->field);
    }

    /**
     * @return string
     */
    protected function buildMultiField()
    {
        return $this->buildField();
    }

    /**
     * @return string
     */
    protected function fieldType()
    {
        $isChoice = in_array($this->field['type'], ['checkbox', 'radio']);
        return Helper::ifTrue($isChoice, 'choice', $this->field['type']);
    }

    /**
     * @return bool
     */
    protected function isFieldValid()
    {
        $missingValues = [];
        $requiredValues = [
            'name', 'type',
        ];
        foreach ($requiredValues as $value) {
            if (isset($this->field[$value])) {
                continue;
            }
            $missingValues[] = $value;
            $this->field['is_valid'] = false;
        }
        if (!empty($missingValues)) {
            glsr_log()
                ->warning('Field is missing: '.implode(', ', $missingValues))
                ->debug($this->field);
        }
        return $this->field['is_valid'];
    }

    /**
     * @param string $className
     * @return array
     */
    protected function mergeFieldArgs($className)
    {
        return $className::merge($this->field);
    }

    /**
     * @return void
     */
    protected function normalizeFieldArgs()
    {
        $className = Helper::buildClassName($this->field['type'], __NAMESPACE__.'\Fields');
        $className = glsr()->filterString('builder/field/'.$this->field['type'], $className);
        if (class_exists($className)) {
            $this->field = $this->mergeFieldArgs($className);
        }
    }

    /**
     * @return void
     */
    protected function normalize()
    {
        if (!$this->isFieldValid()) {
            return;
        }
        $rawType = $this->field['type'];
        $this->field['path'] = $this->field['name'];
        $this->normalizeFieldArgs();
        $this->normalizeFieldId();
        $this->normalizeFieldName();
        $this->field = glsr()->filterArray('field/'.$rawType, $this->field);
    }

    /**
     * @return void
     */
    protected function normalizeFieldId()
    {
        if (isset($this->field['id']) || $this->field['is_raw']) {
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
    protected function normalizeFieldName()
    {
        $this->field['name'] = Str::convertPathToName(
            $this->field['path'],
            $this->getFieldPrefix()
        );
    }
}
