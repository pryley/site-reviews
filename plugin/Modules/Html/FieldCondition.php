<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Contracts\FieldContract;
use GeminiLabs\SiteReviews\Defaults\FieldConditionDefaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;

/**
 * @property string $name
 * @property string $operator
 * @property string $value
 */
class FieldCondition extends Arguments
{
    public FieldContract $field;

    public function __construct(array $values, FieldContract $field)
    {
        $this->field = $field;
        $values = glsr(FieldConditionDefaults::class)->restrict($values);
        parent::__construct($values);
    }

    public function isValid(): bool
    {
        if ($this->name !== $this->field->original_name) {
            return true;
        }
        if (empty($this->operator)) {
            return true;
        }
        $method = Helper::buildMethodName('validate', $this->operator);
        if (method_exists($this, $method)) {
            return call_user_func([$this, $method]);
        }
        return false;
    }

    /**
     * @return array|string
     */
    protected function fieldValue()
    {
        if ($this->field->isMultiField()) {
            return Cast::toArray($this->field->value);
        }
        return $this->field->value;
    }

    protected function isArrayValid(array $values): bool
    {
        $expected = $this->cast('value', 'array');
        $expected = array_map(fn ($val) => Cast::toString($val), $expected);
        $expected = array_values(array_unique($expected));
        $values = array_map(fn ($val) => Cast::toString($val), $values);
        $values = array_values(array_unique($values));
        switch ($this->get('operator')) {
            case 'contains':
                return 0 !== count(array_intersect($expected, $values));
            case 'equals':
                return Arr::compare($expected, $values);
            case 'not':
                return 0 === count(array_intersect($expected, $values));
        }
        return false;
    }

    protected function validateContains(): bool
    {
        if (empty($this->get('value'))) {
            return true;
        }
        $value = $this->fieldValue();
        if (is_array($value)) {
            return $this->isArrayValid($value);
        }
        return str_contains(Cast::toString($value), $this->cast('value', 'string'));
    }

    protected function validateEquals(): bool
    {
        $value = $this->fieldValue();
        if (is_array($value)) {
            return $this->isArrayValid($value);
        }
        return Cast::toString($value) === $this->cast('value', 'string');
    }

    protected function validateGreater(): bool
    {
        return Cast::toFloat($this->field->value) > $this->cast('value', 'float');
    }

    protected function validateLess(): bool
    {
        return Cast::toFloat($this->field->value) < $this->cast('value', 'float');
    }

    protected function validateNot(): bool
    {
        $value = $this->fieldValue();
        if (is_array($value)) {
            return $this->isArrayValid($value);
        }
        return Cast::toString($value) !== $this->cast('value', 'string');
    }
}
