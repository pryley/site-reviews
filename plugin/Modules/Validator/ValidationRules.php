<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Helpers\Str;
use InvalidArgumentException;

/**
 * @see \Illuminate\Validation\Validator (5.3)
 */
trait ValidationRules
{
    /**
     * Get the size of an attribute.
     * @param string $attribute
     * @param mixed $value
     * @return mixed
     */
    abstract protected function getSize($attribute, $value);

    /**
     * Replace all placeholders.
     * @param string $message
     * @return string
     */
    protected function replace($message, array $parameters)
    {
        if (!Str::contains('%s', $message)) {
            return $message;
        }
        return preg_replace_callback('/(%s)/', function () use (&$parameters) {
            foreach ($parameters as $key => $value) {
                return array_shift($parameters);
            }
        }, $message);
    }

    /**
     * Validate that an attribute value was "accepted".
     * This validation rule implies the attribute is "required".
     * @param mixed $value
     * @return bool
     */
    public function validateAccepted($value)
    {
        $acceptable = ['yes', 'on', '1', 1, true, 'true'];
        return $this->validateRequired($value) && in_array($value, $acceptable, true);
    }

    /**
     * Validate the size of an attribute is between a set of values.
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function validateBetween($value, $attribute, array $parameters)
    {
        $this->requireParameterCount(2, $parameters, 'between');
        $size = $this->getSize($attribute, $value);
        return $size >= $parameters[0] && $size <= $parameters[1];
    }

    /**
     * Validate that an attribute value is a valid e-mail address.
     * @param mixed $value
     * @return bool
     */
    public function validateEmail($value)
    {
        return false !== filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Validate the size of an attribute is less than a maximum value.
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function validateMax($value, $attribute, array $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'max');
        return $this->getSize($attribute, $value) <= $parameters[0];
    }

    /**
     * Validate the size of an attribute is greater than a minimum value.
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function validateMin($value, $attribute, array $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'min');
        return $this->getSize($attribute, $value) >= $parameters[0];
    }

    /**
     * Validate that an attribute is numeric.
     * @param mixed $value
     * @return bool
     */
    public function validateNumber($value)
    {
        return is_numeric($value);
    }

    /**
     * Validate that an attribute passes a regular expression check.
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function validateRegex($value, $attribute, array $parameters)
    {
        if (!is_string($value) && !is_numeric($value)) {
            return false;
        }
        $this->requireParameterCount(1, $parameters, 'regex');
        return preg_match($parameters[0], $value) > 0;
    }

    /**
     * Validate that a required attribute exists.
     * @param mixed $value
     * @return bool
     */
    public function validateRequired($value)
    {
        return is_null($value)
            || (is_string($value) && in_array(trim($value), ['', '[]']))
            || (is_array($value) && empty($value))
            ? false
            : true;
    }

    /**
     * Validate that a value is a valid(ish) telephone number.
     * @param mixed $value
     * @return bool
     */
    public function validateTel($value)
    {
        if (!is_string($value) && !is_numeric($value)) {
            return false;
        }
        $digits = (int) preg_match_all('/[0-9]/', $value);
        $hasValidLength = 4 <= $digits && 15 >= $digits;
        return $hasValidLength && preg_match('/^([+]?[\d\s\-\(\)]*)$/', $value) > 0;
    }

    /**
     * Validate that a value is a valid URL.
     * @param  mixed  $value
     * @return bool
     */
    public function validateUrl($value)
    {
        if (!is_string($value)) {
            return false;
        }
        // This pattern is derived from Symfony\Component\Validator\Constraints\UrlValidator (5.0.7).
        // (c) Fabien Potencier <fabien@symfony.com> http://symfony.com
        $pattern = '~^
            (https?)://                                                         # protocol
            (
                ([\pL\pN\pS\-\_\.])+(\.?([\pL\pN]|xn\-\-[\pL\pN-]+)+\.?)        # a domain name
                    |                                                           # or
                \d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}                              # an IPv4 address
            )
            (:[0-9]+)?                                                          # a port (optional)
            (?:/ (?:[\pL\pN\-._\~!$&\'()*+,;=:@]|%[0-9A-Fa-f]{2})* )*           # a path
            (?:\? (?:[\pL\pN\-._\~!$&\'\[\]()*+,;=:@/?]|%[0-9A-Fa-f]{2})* )?    # a query (optional)
            (?:\# (?:[\pL\pN\-._\~!$&\'()*+,;=:@/?]|%[0-9A-Fa-f]{2})* )?        # a fragment (optional)
        $~ixu';
        return preg_match($pattern, $value) > 0;
    }

    /**
     * Require a certain number of parameters to be present.
     * @param int $count
     * @param string $rule
     * @return void
     * @throws InvalidArgumentException
     */
    protected function requireParameterCount($count, array $parameters, $rule)
    {
        if (count($parameters) < $count) {
            throw new InvalidArgumentException("Validation rule $rule requires at least $count parameters.");
        }
    }
}
