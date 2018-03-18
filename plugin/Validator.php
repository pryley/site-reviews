<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 *
 * Much of the code in this class is derived from Illuminate\Validation\Validator (5.3)
 * Copyright (c) <Taylor Otwell>
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews;

use BadMethodCallException;
use InvalidArgumentException;

class Validator
{
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
		'Between',
		'Max',
		'Min',
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
		'Numeric',
	];

	/**
	 * Run the validator's rules against its data.
	 *
	 * @param mixed $data
	 *
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
	 * Add an error message to the validator's collection of errors.
	 *
	 * @param string $attribute
	 * @param string $rule
	 *
	 * @return void
	 */
	protected function addError( $attribute, $rule, array $parameters )
	{
		$message = $this->getMessage( $attribute, $rule, $parameters );

		$this->errors[ $attribute ]['errors'][] = $message;

		if( !isset( $this->errors[ $attribute ]['value'] )) {
			$this->errors[ $attribute ]['value'] = $this->getValue( $attribute );
		}
	}

	/**
	 * Add a failed rule and error message to the collection.
	 *
	 * @param string $attribute
	 * @param string $rule
	 *
	 * @return void
	 */
	protected function addFailure( $attribute, $rule, array $parameters )
	{
		$this->addError( $attribute, $rule, $parameters );

		$this->failedRules[ $attribute ][ $rule ] = $parameters;
	}

	/**
	 * Get the data type of the given attribute.
	 *
	 * @param  string  $attribute
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
	 *
	 * @param string $attribute
	 * @param string $rule
	 *
	 * @return string|null
	 */
	protected function getMessage( $attribute, $rule, array $parameters )
	{
		if( in_array( $rule, $this->sizeRules )) {
			return $this->getSizeMessage( $attribute, $rule, $parameters );
		}

		$lowerRule = $this->snakeCase( $rule );

		return $this->translator( $lowerRule, $rule, $parameters );
	}

	/**
	 * Get a rule and its parameters for a given attribute.
	 *
	 * @param string       $attribute
	 * @param string|array $rules
	 *
	 * @return array|null
	 */
	protected function getRule( $attribute, $rules )
	{
		if( !array_key_exists( $attribute, $this->rules ))return;

		$rules = (array) $rules;

		foreach( $this->rules[ $attribute ] as $rule ) {
			list( $rule, $parameters ) = $this->parseRule( $rule );

			if( in_array( $rule, $rules )) {
				return [ $rule, $parameters ];
			}
		}
	}

	/**
	 * Get the size of an attribute.
	 *
	 * @param string $attribute
	 * @param mixed  $value
	 *
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
	 *
	 * @param string $attribute
	 * @param string $rule
	 *
	 * @return string|null
	 */
	protected function getSizeMessage( $attribute, $rule, array $parameters )
	{
		$lowerRule = $this->snakeCase( $rule );
		$type = $this->getAttributeType( $attribute );
		return $this->translator( $lowerRule.'.'.$type, $rule, $parameters );
	}

	/**
	 * Get the value of a given attribute.
	 *
	 * @param string $attribute
	 *
	 * @return mixed
	 */
	protected function getValue( $attribute )
	{
		if( isset( $this->data[ $attribute ] )) {
			return $this->data[ $attribute ];
		}
	}

	/**
	 * Determine if the given attribute has a rule in the given set.
	 *
	 * @param string       $attribute
	 * @param string|array $rules
	 *
	 * @return bool
	 */
	protected function hasRule( $attribute, $rules )
	{
		return !is_null( $this->getRule( $attribute, $rules ));
	}

	/**
	 * Normalize the provided data to an array.
	 *
	 * @param mixed $data
	 *
	 * @return $this
	 */
	protected function normalizeData( $data )
	{
		// If an object was provided, get its public properties
		if( is_object( $data )) {
			$this->data = get_object_vars( $data );
		}
		else {
			$this->data = $data;
		}

		return $this;
	}

	/**
	 * Parse a parameter list.
	 *
	 * @param string $rule
	 * @param string $parameter
	 *
	 * @return array
	 */
	protected function parseParameters( $rule, $parameter )
	{
		if( strtolower( $rule ) == 'regex' ) {
			return [ $parameter ];
		}

		return str_getcsv( $parameter );
	}

	/**
	 * Extract the rule name and parameters from a rule.
	 *
	 * @param string $rule
	 *
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

		return [ $rule, $parameters ];
	}

	/**
	 * Replace all placeholders for the between rule.
	 *
	 * @param string $message
	 *
	 * @return string
	 */
	protected function replaceBetween( $message, array $parameters )
	{
		return str_replace([':min', ':max'], $parameters, $message );
	}

	/**
	 * Replace all placeholders for the max rule.
	 *
	 * @param string $message
	 *
	 * @return string
	 */
	protected function replaceMax( $message, array $parameters )
	{
		return str_replace( ':max', $parameters[0], $message );
	}

	/**
	 * Replace all placeholders for the min rule.
	 *
	 * @param string $message
	 *
	 * @return string
	 */
	protected function replaceMin( $message, array $parameters )
	{
		return str_replace( ':min', $parameters[0], $message );
	}

	/**
	 * Require a certain number of parameters to be present.
	 *
	 * @param int    $count
	 * @param string $rule
	 *
	 * @return void
	 * @throws InvalidArgumentException
	 */
	protected function requireParameterCount( $count, array $parameters, $rule )
	{
		if( count( $parameters ) < $count ) {
			throw new InvalidArgumentException( "Validation rule $rule requires at least $count parameters." );
		}
	}

	/**
	 * Set the validation rules.
	 *
	 * @return $this
	 */
	protected function setRules( array $rules )
	{
		foreach( $rules as $key => $rule ) {
			$rules[ $key ] = is_string( $rule ) ? explode( '|', $rule ) : $rule;
		}

		$this->rules = $rules;

		return $this;
	}

	/**
	 * Check if we should stop further validations on a given attribute.
	 *
	 * @param string $attribute
	 *
	 * @return bool
	 */
	protected function shouldStopValidating( $attribute )
	{
		return $this->hasRule( $attribute, $this->implicitRules )
			&& isset( $this->failedRules[ $attribute ] )
			&& array_intersect( array_keys( $this->failedRules[ $attribute ] ), $this->implicitRules );
	}

	/**
	 * Convert a string to snake case.
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	protected function snakeCase( $string )
	{
		if( !ctype_lower( $string )) {
			$string = preg_replace( '/\s+/u', '', $string );
			$string = preg_replace( '/(.)(?=[A-Z])/u', '$1_', $string );
			$string = mb_strtolower( $string, 'UTF-8' );
		}

		return $string;
	}

	/**
	 * Returns a translated message for the attribute
	 *
	 * @param string $key
	 * @param string $rule
	 *
	 * @return string|null
	 */
	protected function translator( $key, $rule, array $parameters )
	{
		$strings = glsr_resolve( 'Strings' )->validation();

		$message = isset( $strings[ $key ] )
			? $strings[ $key ]
			: false;

		if( !$message )return;

		if( method_exists( $this, $replacer = "replace{$rule}" )) {
			$message = $this->$replacer( $message, $parameters );
		}

		return $message;
	}

	// Rules Validation
	// ---------------------------------------------------------------------------------------------

	/**
	 * Validate that an attribute was "accepted".
	 *
	 * This validation rule implies the attribute is "required".
	 *
	 * @param string $attribute
	 * @param mixed  $value
	 *
	 * @return bool
	 */
	protected function validateAccepted( $attribute, $value )
	{
		$acceptable = ['yes', 'on', '1', 1, true, 'true'];

		return $this->validateRequired( $attribute, $value ) && in_array( $value, $acceptable, true );
	}

	/**
	 * Validate a given attribute against a rule.
	 *
	 * @param string $attribute
	 * @param string $rule
	 *
	 * @return void
	 * @throws BadMethodCallException
	 */
	protected function validateAttribute( $attribute, $rule )
	{
		list( $rule, $parameters ) = $this->parseRule( $rule );

		if( $rule == '' )return;

		$value = $this->getValue( $attribute );

		// is the value filled or is the attribute required?
		// - removed $validatable assignment
		$this->validateRequired( $attribute, $value ) || in_array( $rule, $this->implicitRules );

		$method = "validate{$rule}";

		if( !method_exists( $this, $method )) {
			throw new BadMethodCallException( "Method [$method] does not exist." );
		}

		if( !$this->$method( $attribute, $value, $parameters )) {
			$this->addFailure( $attribute, $rule, $parameters );
		}
	}

	/**
	 * Validate the size of an attribute is between a set of values.
	 *
	 * @param string $attribute
	 * @param mixed  $value
	 *
	 * @return bool
	 */
	protected function validateBetween( $attribute, $value, array $parameters )
	{
		$this->requireParameterCount( 2, $parameters, 'between' );

		$size = $this->getSize( $attribute, $value );

		return $size >= $parameters[0] && $size <= $parameters[1];
	}

	/**
	 * Validate that an attribute is a valid e-mail address.
	 *
	 * @param string $attribute
	 * @param mixed  $value
	 *
	 * @return bool
	 */
	protected function validateEmail( $attribute, $value )
	{
		return filter_var( $value, FILTER_VALIDATE_EMAIL ) !== false;
	}

	/**
	 * Validate the size of an attribute is less than a maximum value.
	 *
	 * @param string $attribute
	 * @param mixed  $value
	 *
	 * @return bool
	 */
	protected function validateMax( $attribute, $value, array $parameters )
	{
		$this->requireParameterCount( 1, $parameters, 'max' );

		return $this->getSize( $attribute, $value ) <= $parameters[0];
	}

	/**
	 * Validate the size of an attribute is greater than a minimum value.
	 *
	 * @param string $attribute
	 * @param mixed  $value
	 *
	 * @return bool
	 */
	protected function validateMin( $attribute, $value, array $parameters )
	{
		$this->requireParameterCount( 1, $parameters, 'min' );

		return $this->getSize( $attribute, $value ) >= $parameters[0];
	}

	/**
	 * Validate that an attribute is numeric.
	 *
	 * @param string $attribute
	 * @param mixed  $value
	 *
	 * @return bool
	 */
	protected function validateNumeric( $attribute, $value )
	{
		return is_numeric( $value );
	}

	/**
	 * Validate that a required attribute exists.
	 *
	 * @param string $attribute
	 * @param mixed  $value
	 *
	 * @return bool
	 */
	protected function validateRequired( $attribute, $value )
	{
		return is_null( $value )
			|| ( is_string( $value ) && trim( $value ) === '' )
			|| ( is_array( $value ) && count( $value ) < 1 )
			? false
			: true;
	}
}
