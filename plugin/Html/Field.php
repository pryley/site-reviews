<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Html;

use Exception;
use GeminiLabs\SiteReviews\App;
use ReflectionException;

class Field
{
	/**
	 * @var App
	 */
	protected $app;

	/**
	 * @var array
	 */
	protected $args;

	/**
	 * @var array
	 */
	protected $dependencies;

	public function __construct( App $app )
	{
		$this->app          = $app;
		$this->args         = [];
		$this->dependencies = [];
	}

	/**
	 * @param string $property
	 * @return mixed
	 * @throws Exception
	 */
	public function __get( $property )
	{
		if( in_array( $property, ['args', 'dependencies'] )) {
			return $this->$property;
		}
		throw new Exception( 'Invalid ' . __CLASS__ . ' property: ' . $property );
	}

	/**
	 * Get a specific Field
	 *
	 * @return mixed GeminiLabs\SiteReviews\Html\Fields\*
	 */
	public function getField( array $args = [] )
	{
		if( empty( $args )) {
			$args = $this->args;
		}

		$className = sprintf( 'GeminiLabs\SiteReviews\Html\Fields\%s', ucfirst( $args['type'] ));

		if( !class_exists( $className )) {
			throw new ReflectionException( "Class does not exist: {$className}" );
		}

		return (new $className( $args ));
	}

	/**
	 * Normalize the field arguments
	 *
	 * @return $this
	 */
	public function normalize( array $args = [] )
	{
		$defaults = [
			'after'       => '',
			'attributes'  => '',
			'before'      => '',
			'class'       => '',
			'default'     => null,
			'depends'     => null,
			'desc'        => '',
			'errors'      => [],
			'inline'      => false,
			'label'       => '',
			'name'        => '',
			'options'     => [],
			'path'        => '',
			'placeholder' => '',
			'prefix'      => '',
			'render'      => true,
			'suffix'      => null,
			'type'        => 'text',
			'value'       => '',
		];

		$args = $atts = wp_parse_args( $args, $defaults );

		$args['attributes'] = $this->parseAttributes( $atts );
		$args['id']         = $this->parseId( $atts );
		$args['inline']     = $this->parseInline( $atts );
		$args['type']       = $this->parseType( $atts );
		$args['name']       = $this->parseName( $atts );
		$args['options']    = (array) $atts['options']; // make sure this is always an array
		$args['path']       = $atts['name'];
		$args['prefix']     = $this->parsePrefix( $atts );
		$args['value']      = $this->parseValue( $atts );

		$this->args = $args;

		$this->dependencies = $this->getField( $args )->dependencies;

		$this->setDataDepends();

		$this->checkForErrors( $atts );

		return $this;
	}

	/**
	 * Render the field
	 * @return string
	 */
	public function render()
	{
		if( $this->args['render'] === false )return;

		$field = $this->getField();

		$class  = 'glsr-field';
		$class .= $this->args['errors'] ? ' glsr-has-error' : '';

		$renderedString = '%s';

		if( ( isset( $field->args['required'] ) && $field->args['required'] )
			|| ( isset( $field->args['attributes']['required'] ) || in_array( 'required', $field->args['attributes'] )) ) {
			$class .= ' glsr-required';
		}

		if( !in_array( $field->args['type'], ['hidden', 'honeypot'] )) {
			$renderedString = sprintf( '<div class="%s">%%s</div>', $class );
		}

		$rendered = sprintf( $renderedString,
			$this->args['before'] .
			$field->generateLabel() .
			$field->render() .
			$this->args['after'] .
			$this->args['errors']
		);
		return apply_filters( 'site-reviews/rendered/field', $rendered, $field->args['type'] );
	}

	/**
	 * Reset the Field
	 *
	 * @return self
	 */
	public function reset()
	{
		$this->args = [];

		return $this;
	}

	/**
	 * Check for form submission field errors
	 *
	 * @return void
	 */
	protected function checkForErrors( array $atts )
	{
		$args = $this->args;

		if( !array_key_exists( $atts['name'], $args['errors'] )) {
			$this->args['errors'] = ''; // set to an empty string
			return;
		}

		$field_errors = $args['errors'][ $atts['name'] ];

		$errors = array_reduce( $field_errors['errors'], function( $carry, $error ) {
			return $carry . sprintf( '<span class="glsr-field-error">%s</span> ', $error );
		});

		$this->args['errors'] = sprintf( '<span class="glsr-field-errors">%s</span>', $errors );
	}

	/**
	 * Parse the field attributes and convert to an array if needed
	 *
	 * @return array
	 */
	protected function parseAttributes( array $args )
	{
		if( empty( $args['attributes'] )) {
			return [];
		}

		$attributes = (array) $args['attributes'];

		foreach( $attributes as $key => $value ) {
			if( is_string( $key ))continue;
			unset( $attributes[ $key ] );
			if( !isset( $attributes[ $value ] )) {
				$attributes[ $value ] = '';
			}
		}

		return $attributes;
	}

	/**
	 * Parse the field ID from the field path
	 *
	 * @return null|string
	 */
	protected function parseId( array $args )
	{
		if( isset( $args['id'] ) && !$args['id'] )return;

		!$args['suffix'] ?: $args['suffix'] = "-{$args['suffix']}";

		return str_replace( ['[]','[',']','.'], ['','-','','-'], $this->parseName( $args ) . $args['suffix'] );
	}

	/**
	 * Parse the field inline
	 *
	 * @return bool
	 */
	protected function parseInline( array $args )
	{
		return false !== stripos( $args['type'], '_inline' )
			? true
			: $args['inline'];
	}

	/**
	 * Parse the field name
	 *
	 * @return string
	 */
	protected function parseName( array $args )
	{
		$name   = $args['name'];
		$prefix = $this->parsePrefix( $args );

		if( $prefix === false ) {
			return $name;
		}

		$paths = explode( '.', $name );

		return array_reduce( $paths, function( $result, $value ) {
			return $result .= "[$value]";
		}, $prefix );
	}

	/**
	 * Parse the field prefix
	 *
	 * @return string|false
	 */
	protected function parsePrefix( array $args )
	{
		$prefix = $args['prefix'];

		return ( '' === trim( $prefix ) && $prefix !== false )
			? $this->app->make( 'Database' )->getOptionName()
			: $prefix;
	}

	/**
	 * Parse the field type
	 *
	 * @return string
	 */
	protected function parseType( array $args )
	{
		$type = $args['type'];

		return false !== stripos( $type, '_inline' )
			? str_replace( '_inline', '', $type )
			: $type;
	}

	/**
	 * Parse the field value
	 *
	 * @return string
	 */
	protected function parseValue( array $args )
	{
		$default = $args['default'];
		$name    = $args['name'];
		$prefix  = $args['prefix'];
		$value   = $args['value'];

		if( $default == ':placeholder' ) {
			$default = '';
		}

		return ( !empty( $value ) || !$name || $prefix === false )
			? $value
			: $default;
	}

	/**
	 * Get the [data-depends] attribute
	 *
	 * @return array|null
	 */
	public function getDataDepends()
	{
		return $this->setDataDepends();
	}

	/**
	 * Set the field value
	 *
	 * @return self
	 */
	public function setValue()
	{
		$name    = $this->args['path'];
		$default = $this->args['default'];

		if( !$name ) {
			return $this;
		}

		if( $default == ':placeholder' ) {
			$default = '';
		}

		$value = glsr_resolve( 'Database' )->getOption( $name, $default );

		if( !empty( $value ) || $this->args['type'] == 'checkbox' ) {
			$this->args['value'] = $value;
		}

		return $this;
	}

	/**
	 * Set the [data-depends] attribute
	 *
	 * @return null|array
	 */
	protected function setDataDepends()
	{
		if( !( $depends = $this->args['depends'] ))return;

		$name  = $depends;
		$value = true;

		if( is_array( $depends )) {
			reset( $depends );
			$name  = key( $depends );
			$value = $depends[ $name ];
		}

		$name = $this->parseName([
			'name'   => $name,
			'prefix' => $this->args['prefix'],
		]);

		return $this->args['attributes']['data-depends'] = [
			'name'  => $name,
			'value' => $value,
		];
	}
}
