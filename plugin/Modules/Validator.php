<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Defaults\ValidationStringsDefaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Validator\ValidationRules;

/**
 * @see \Illuminate\Validation\Validator (5.3)
 */
class Validator
{
    use ValidationRules;

    /**
     * @var array
     */
    public $errors = [];

    /**
     * The data under validation.
     *
     * @var array
     */
    protected $data = [];

    /**
     * The failed validation rules.
     *
     * @var array
     */
    protected $failedRules = [];

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    protected $rules = [];

    /**
     * The size related validation rules.
     *
     * @var array
     */
    protected $sizeRules = [
        'Between', 'Max', 'Min',
    ];

    /**
     * The validation rules that imply the field is required.
     *
     * @var array
     */
    protected $implicitRules = [
        'Required',
    ];

    /**
     * The numeric related validation rules.
     *
     * @var array
     */
    protected $numericRules = [
        'Number',
    ];

    /**
     * Run the validator's rules against its data.
     *
     * @throws \BadMethodCallException
     */
    public function validate(array $data, array $rules = []): array
    {
        $this->data = $data;
        $this->setRules($rules);
        foreach ($this->rules as $attribute => $rules) {
            foreach ($rules as $rule) {
                $this->validateAttribute($attribute, $rule);
                if ($this->shouldStopValidating($attribute)) {
                    break;
                }
            }
        }
        return $this->errors;
    }

    /**
     * Validate a given attribute against a rule.
     *
     * @throws \BadMethodCallException
     */
    public function validateAttribute(string $attribute, string $rule): void
    {
        [$rule, $parameters] = $this->parseRule($rule);
        if ('' === $rule) {
            return;
        }
        $value = $this->getValue($attribute);
        $method = Helper::buildMethodName('validate', $rule);
        if (!method_exists($this, $method)) {
            throw new \BadMethodCallException("Method [$method] does not exist.");
        }
        if (!$this->$method($value, $attribute, $parameters)) {
            $this->addFailure($attribute, $rule, $parameters);
        }
    }

    /**
     * Add an error message to the validator's collection of errors.
     */
    protected function addError(string $attribute, string $rule, array $parameters): void
    {
        $message = $this->getMessage($attribute, $rule, $parameters);
        $this->errors[$attribute][] = $message;
    }

    /**
     * Add a failed rule and error message to the collection.
     */
    protected function addFailure(string $attribute, string $rule, array $parameters): void
    {
        $this->addError($attribute, $rule, $parameters);
        $this->failedRules[$attribute][$rule] = $parameters;
    }

    /**
     * Get the data type of the given attribute.
     */
    protected function getAttributeType(string $attribute): string
    {
        $type = $this->hasRule($attribute, $this->numericRules) ? '' : 'length';
        return glsr()->filterString("validation/type/{$attribute}", $type, $attribute);
    }

    /**
     * Get the validation message for an attribute and rule.
     */
    protected function getMessage(string $attribute, string $rule, array $parameters): ?string
    {
        if (in_array($rule, $this->sizeRules)) {
            return $this->getSizeMessage($attribute, $rule, $parameters);
        }
        $lowerRule = Str::snakeCase($rule);
        return $this->translator($lowerRule, $parameters);
    }

    /**
     * Get a rule and its parameters for a given attribute.
     */
    protected function getRule(string $attribute, array $rules): ?array
    {
        if (!array_key_exists($attribute, $this->rules)) {
            return null;
        }
        $rules = (array) $rules;
        foreach ($this->rules[$attribute] as $rule) {
            [$rule, $parameters] = $this->parseRule($rule);
            if (in_array($rule, $rules)) {
                return [$rule, $parameters];
            }
        }
        return null;
    }

    /**
     * Get the size of an attribute.
     *
     * @param mixed $value
     */
    protected function getSize(string $attribute, $value): int
    {
        $hasNumeric = $this->hasRule($attribute, $this->numericRules);
        if (is_numeric($value) && $hasNumeric) {
            return (int) $value;
        }
        if (is_array($value)) {
            return count($value);
        }
        if (is_array($json = json_decode($value))) {
            return count($json);
        }
        return mb_strlen((string) $value);
    }

    /**
     * Get the proper error message for an attribute and size rule.
     */
    protected function getSizeMessage(string $attribute, string $rule, array $parameters): string
    {
        $type = $this->getAttributeType($attribute);
        $lowerRule = Str::snakeCase($rule.$type);
        return $this->translator($lowerRule, $parameters);
    }

    /**
     * Get the value of a given attribute.
     *
     * @return mixed
     */
    protected function getValue(string $attribute)
    {
        return $this->data[$attribute] ?? '';
    }

    /**
     * Determine if the given attribute has a rule in the given set.
     */
    protected function hasRule(string $attribute, array $rules): bool
    {
        return !is_null($this->getRule($attribute, $rules));
    }

    /**
     * Parse a parameter list.
     */
    protected function parseParameters(string $rule, string $parameter): array
    {
        return 'regex' === strtolower($rule)
            ? [$parameter]
            : str_getcsv($parameter);
    }

    /**
     * Extract the rule name and parameters from a rule.
     */
    protected function parseRule(string $rule): array
    {
        $parameters = [];
        if (str_contains($rule, ':')) {
            [$rule, $parameter] = explode(':', $rule, 2);
            $parameters = $this->parseParameters($rule, $parameter);
        }
        $rule = Str::camelCase($rule);
        return [$rule, $parameters];
    }

    /**
     * Set the validation rules.
     */
    protected function setRules(array $rules): void
    {
        foreach ($rules as $key => $rule) {
            $validationRules = is_string($rule) ? explode('|', $rule) : $rule;
            $validationRules = array_filter(Arr::consolidate($validationRules));
            // unset rules if the attribute is not required and the value is an empty string
            if (empty(array_intersect(['accepted', 'required'], $validationRules)) && '' === $this->getValue($key)) {
                $validationRules = [];
            }
            $rules[$key] = $validationRules;
        }
        $this->rules = $rules;
    }

    /**
     * Check if we should stop further validations on a given attribute.
     */
    protected function shouldStopValidating(string $attribute): bool
    {
        return $this->hasRule($attribute, $this->implicitRules)
            && isset($this->failedRules[$attribute])
            && array_intersect(array_keys($this->failedRules[$attribute]), $this->implicitRules);
    }

    /**
     * Returns a translated message for the attribute.
     */
    protected function translator($key, array $parameters): string
    {
        $strings = glsr(ValidationStringsDefaults::class)->defaults();
        $string = $strings[$key] ?? 'error';
        return $this->replace($string, $parameters);
    }
}
