<?php

namespace GeminiLabs\SiteReviews\Modules;

use BadMethodCallException;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Strings;
use GeminiLabs\SiteReviews\Modules\Validator\ValidationRules;
use InvalidArgumentException;

/**
 * Much of the code in this class is derived from \Illuminate\Validation\Validator (5.3)
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
	 * @var array
	 */
	protected $data = [];

	/**
	 * The failed validation rules.
	 * @var array
	 */
	protected $failedRules = [];

	/**
	 * The rules to be applied to the data.
	 * @var array
	 */
	protected $rules = [];

	/**
	 * The size related validation rules.
	 * @var array
	 */
	protected $sizeRules = [
		'Between', 'Max', 'Min',
	];

	/**
	 * The validation rules that imply the field is required.
	 * @var array
	 */
	protected $implicitRules = [
		'Required',
	];

	/**
	 * The numeric related validation rules.
	 * @var array
	 */
	protected $numericRules = [
		'Numeric',
	];

	/**
	 * Run the validator's rules against its data.
	 * @param mixed $data
	 * @return array
	 */
	public function validate( $data, array $rules = [] )
	{
		$this->normalizeData( $data );
		$this->setRules( $rules );
		foreach( $this->rules as $attribute => $rules ) {
			foreach( $rules as $rule ) {
				$this->validateAttribute( $attribute, $rule );
				if( $this->shouldStopValidating( $attribute ))break;
			}
		}
		return $this->errors;
	}

	/**
	 * Validate a given attribute against a rule.
	 * @param string $attribute
	 * @param string $rule
	 * @return void
	 * @throws BadMethodCallException
	 */
	public function validateAttribute( $attribute, $rule )
	{
		list( $rule, $parameters ) = $this->parseRule( $rule );
		if( $rule == '' )return;
		$value = $this->getValue( $attribute );
		$this->validateRequired( $attribute, $value ) || in_array( $rule, $this->implicitRules );
		if( !method_exists( $this, $method = 'validate'.$rule )) {
			throw new BadMethodCallException( "Method [$method] does not exist." );
		}
		if( !$this->$method( $attribute, $value, $parameters )) {
			$this->addFailure( $attribute, $rule, $parameters );
		}
	}

	/**
	 * Add an error message to the validator's collection of errors.
	 * @param string $attribute
	 * @param string $rule
	 * @return void
	 */
	protected function addError( $attribute, $rule, array $parameters )
	{
		$message = $this->getMessage( $attribute, $rule, $parameters );
		$this->errors[$attribute]['errors'][] = $message;
		if( !isset( $this->errors[$attribute]['value'] )) {
			$this->errors[$attribute]['value'] = $this->getValue( $attribute );
		}
	}

	/**
	 * Add a failed rule and error message to the collection.
	 * @param string $attribute
	 * @param string $rule
	 * @return void
	 */
	protected function addFailure( $attribute, $rule, array $parameters )
	{
		$this->addError( $attribute, $rule, $parameters );
		$this->failedRules[$attribute][$rule] = $parameters;
	}

	/**
	 * Get the data type of the given attribute.
	 * @param string $attribute
	 * @return string
	 */
	protected function getAttributeType( $attribute )
	{
		return $this->hasRule( $attribute, $this->numericRules )
			? 'numeric'
			: 'string';
	}

	/**
	 * Get the validation message for an attribute and rule.
	 * @param string $attribute
	 * @param string $rule
	 * @return string|null
	 */
	protected function getMessage( $attribute, $rule, array $parameters )
	{
		if( in_array( $rule, $this->sizeRules )) {
			return $this->getSizeMessage( $attribute, $rule, $parameters );
		}
		$lowerRule = glsr( Helper::class )->snakeCase( $rule );
		return $this->translator( $lowerRule, $rule, $attribute, $parameters );
	}

	/**
	 * Get a rule and its parameters for a given attribute.
	 * @param string $attribute
	 * @param string|array $rules
	 * @return array|null
	 */
	protected function getRule( $attribute, $rules )
	{
		if( !array_key_exists( $attribute, $this->rules ))return;
		$rules = (array) $rules;
		foreach( $this->rules[$attribute] as $rule ) {
			list( $rule, $parameters ) = $this->parseRule( $rule );
			if( in_array( $rule, $rules )) {
				return [$rule, $parameters];
			}
		}
	}

	/**
	 * Get the size of an attribute.
	 * @param string $attribute
	 * @param mixed $value
	 * @return mixed
	 */
	protected function getSize( $attribute, $value )
	{
		$hasNumeric = $this->hasRule( $attribute, $this->numericRules );
		if( is_numeric( $value ) && $hasNumeric ) {
			return $value;
		}
		elseif( is_array( $value )) {
			return count( $value );
		}
		return mb_strlen( $value );
	}

	/**
	 * Get the proper error message for an attribute and size rule.
	 * @param string $attribute
	 * @param string $rule
	 * @return string|null
	 */
	protected function getSizeMessage( $attribute, $rule, array $parameters )
	{
		$lowerRule = glsr( Helper::class )->snakeCase( $rule );
		$type = $this->getAttributeType( $attribute );
		$lowerRule .= '.'.$type;
		return $this->translator( $lowerRule, $rule, $attribute, $parameters );
	}

	/**
	 * Get the value of a given attribute.
	 * @param string $attribute
	 * @return mixed
	 */
	protected function getValue( $attribute )
	{
		if( isset( $this->data[$attribute] )) {
			return $this->data[$attribute];
		}
	}

	/**
	 * Determine if the given attribute has a rule in the given set.
	 * @param string $attribute
	 * @param string|array $rules
	 * @return bool
	 */
	protected function hasRule( $attribute, $rules )
	{
		return !is_null( $this->getRule( $attribute, $rules ));
	}

	/**
	 * Normalize the provided data to an array.
	 * @param mixed $data
	 * @return $this
	 */
	protected function normalizeData( $data )
	{
		$this->data = is_object( $data )
			? get_object_vars( $data )
			: $data;
		return $this;
	}

	/**
	 * Parse a parameter list.
	 * @param string $rule
	 * @param string $parameter
	 * @return array
	 */
	protected function parseParameters( $rule, $parameter )
	{
		if( strtolower( $rule ) == 'regex' ) {
			return [$parameter];
		}
		return str_getcsv( $parameter );
	}

	/**
	 * Extract the rule name and parameters from a rule.
	 * @param string $rule
	 * @return array
	 */
	protected function parseRule( $rule )
	{
		$parameters = [];
		// example: {rule}:{parameters}
		if( strpos( $rule, ':' ) !== false ) {
			list( $rule, $parameter ) = explode( ':', $rule, 2 );
			// example: {parameter1,parameter2,...}
			$parameters = $this->parseParameters( $rule, $parameter );
		}
		$rule = ucwords( str_replace( ['-', '_'], ' ', trim( $rule )));
		$rule = str_replace( ' ', '', $rule );
		return [$rule, $parameters];
	}

	/**
	 * Set the validation rules.
	 * @return $this
	 */
	protected function setRules( array $rules )
	{
		foreach( $rules as $key => $rule ) {
			$rules[$key] = is_string( $rule )
				? explode( '|', $rule )
				: $rule;
		}
		$this->rules = $rules;
		return $this;
	}

	/**
	 * Check if we should stop further validations on a given attribute.
	 * @param string $attribute
	 * @return bool
	 */
	protected function shouldStopValidating( $attribute )
	{
		return $this->hasRule( $attribute, $this->implicitRules )
			&& isset( $this->failedRules[$attribute] )
			&& array_intersect( array_keys( $this->failedRules[$attribute] ), $this->implicitRules );
	}

	/**
	 * Returns a translated message for the attribute
	 * @param string $key
	 * @param string $rule
	 * @param string $attribute
	 * @return string|null
	 */
	protected function translator( $key, $rule, $attribute, array $parameters )
	{
		$strings = glsr( Strings::class )->validation();
		$message = isset( $strings[$key] )
			? $strings[$key]
			: false;
		if( !$message )return;
		$message = str_replace( ':attribute', $attribute, $message );
		if( method_exists( $this, $replacer = 'replace'.$rule )) {
			$message = $this->$replacer( $message, $parameters );
		}
		return $message;
	}
}
