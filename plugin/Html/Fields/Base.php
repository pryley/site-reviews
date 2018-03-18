<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Html\Fields;

use Exception;
use GeminiLabs\SiteReviews\Html\Normalize;

abstract class Base
{
	/**
	 * @var array
	 */
	protected $args;

	/**
	 * @var array
	 */
	protected $dependencies = [];

	/**
	 * Whether the field has multiple values
	 *
	 * @var bool
	 */
	protected $multi = false;

	/**
	 * Whether the field is rendered outside of the form table
	 *
	 * @var bool
	 */
	protected $outside = false;

	/**
	 * The field element tag (i.e. "input")
	 *
	 * @var string
	 */
	protected $element;

	public function __construct( array $args = [] )
	{
		$this->args = $args;
	}

	/**
	 * @param string $property
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public function __get( $property )
	{
		if( in_array( $property, [
			'args',
			'dependencies',
			'element',
			'multi',
			'outside',
		])) {
			return $this->$property;
		}
		throw new Exception( 'Invalid ' . __CLASS__ . ' property: ' . $property );
	}

	/**
	 * Generate the field description
	 *
	 * @param bool $paragraph
	 *
	 * @return null|string
	 */
	public function generateDescription( $paragraph = true )
	{
		if( !empty( $this->args['desc'] )) {
			return sprintf( '<p class="description">%s</p>', $this->args['desc'] );
		}
		else if( !empty( $this->args['description'] )) {
			return sprintf( '<small>%s</small>', $this->args['description'] );
		}
	}

	/**
	 * Generate the field label
	 *
	 * @return null|string
	 */
	public function generateLabel()
	{
		if( empty( $this->args['label'] ))return;

		$for = !!$this->args['id']
			? " for=\"{$this->args['id']}\""
			: '';

		return sprintf( '<label%s>%s</label>', $for, $this->args['label'] );
	}

	/**
	 * Render this field type
	 *
	 * @return string
	 */
	abstract public function render( array $defaults = [] );

	/**
	 * Convert a value to camel case.
	 *
	 * @param string $value
	 *
	 * @return string
	 */
	protected function camelCase( $value )
	{
		$value = ucwords( str_replace( ['-', '_'], ' ', $value ));

		return lcfirst( str_replace( ' ', '', $value ));
	}

	/**
	 * Implode the field attributes
	 *
	 * @return string
	 */
	protected function implodeAttributes( $defaults = [] )
	{
		return $this->normalize( $defaults, 'implode' );
	}

	/**
	 * Implode multi-field items
	 *
	 * @return null|string
	 */
	protected function implodeOptions( $method = 'select_option', $default = null )
	{
		$this->args['default'] ?: $this->args['default'] = $default;

		$method = $this->camelCase( $method );

		$method = method_exists( $this, $method )
			? $method
			: 'selectOption';

		$i = 0;

		if( $method === 'singleInput' ) {

			if( !isset( $this->args['options'] ) || empty( $this->args['options'] ))return;

			// hack to make sure unset single checkbox values start at 1 instead of 0
			if( key( $this->args['options'] ) === 0 ) {
				$options = ['1' => $this->args['options'][0]];
				$this->args['options'] = $options;
			}

			return $this->singleInput();
		}

		return array_reduce( array_keys( $this->args['options'] ), function( $carry, $key ) use ( &$i, $method ) {
			return $carry .= $this->$method( $key, $i++ );
		});
	}

	/**
	 * Normalize attributes for this specific field type
	 *
	 * @param bool|string $implode
	 * @return array|string
	 */
	protected function normalize( array $defaults = [], $implode = false )
	{
		$args = $this->mergeAttributesWith( $defaults );
		$normalize = new Normalize;
		return ( $this->element && method_exists( $normalize, $this->element ))
			? $normalize->{$this->element}( $args, $implode )
			: ( $implode === false ? [] : '' );
	}

	/**
	 * Merge and overwrite empty $this->args values with $defaults
	 *
	 * @return array
	 */
	protected function mergeAttributesWith( array $defaults )
	{
		// similar to array_merge except overwrite empty values
		foreach( $defaults as $key => $value ) {
			if( isset( $this->args[ $key ] ) && !empty( $this->args[ $key ] ))continue;
			$this->args[ $key ] = $value;
		}

		$attributes = $this->args['attributes'];

		// prioritize $attributes over $this->args, don't worry about duplicates
		return array_merge( $this->args, $attributes );
	}

	/**
	 * Generate checkboxes and radios
	 *
	 * @param string $optionKey
	 * @param string $number
	 * @param string $type
	 *
	 * @return null|string
	 */
	protected function multiInput( $optionKey, $number, $type = 'radio' )
	{
		$args = $this->multiInputArgs( $type, $optionKey, $number );

		if( !$args )return;

		$attributes = '';

		foreach( $args['attributes'] as $key => $val ) {
			$attributes .= sprintf( '%s="%s" ', $key, $val );
		}

		$value = $args['value'];
		if( is_array( $value )) {
			$value = in_array( $args['attributes']['value'], $value )
				? $args['attributes']['value']
				: false;
		}

		return sprintf( '<li><label for="%s"><input %s%s/> %s</label></li>',
			$args['attributes']['id'],
			$attributes,
			checked( $value, $args['attributes']['value'], false ),
			$args['label']
		);
	}

	/**
	 * Build the checkbox/radio args
	 *
	 * @param string $type
	 * @param string $optionName
	 * @param string $number
	 *
	 * @return array|null
	 */
	protected function multiInputArgs( $type, $optionName, $number )
	{
		$defaults = [
			'class' => '',
			'name'  => '',
			'type'  => $type,
			'value' => '',
		];

		$args = [];

		$value = $this->args['options'][ $optionName ];

		if( is_array( $value )) {
			$args = $value;
		}

		if( is_string( $value )) {
			$label = $value;
		}

		isset( $args['name'] ) ?: $args['name'] = $optionName;
		isset( $args['value'] ) ?: $args['value'] = $optionName;

		$args = wp_parse_args( $args, $defaults );

		if( empty( $label ) || $args['name'] === '' )return;

		$args['id']   = $this->args['id'] . "-{$number}";
		$args['name'] = $this->args['name'] . ( $type === 'checkbox' && $this->multi ? '[]' : '' );

		$args = array_filter( $args, function( $value ) {
			return $value !== '';
		});

		if( is_array( $this->args['value'] )) {
			// if( in_array( $args['value'], $this->args['value'] )) {
			$this->args['default'] = $this->args['value'];
			// }
		}
		else if( $this->args['value'] ) {
			$this->args['default'] = $this->args['value'];
		}
		else if( $type == 'radio' && !$this->args['default'] ) {
			$this->args['default'] = 0;
		}

		return [
			'attributes' => $args,
			'label'      => $label,
			'value'      => $this->args['default'],
		];
	}

	/**
	 * Generate checkboxes
	 *
	 * @param string $optionKey
	 * @param string $number
	 *
	 * @return null|string
	 */
	protected function multiInputCheckbox( $optionKey, $number )
	{
		return $this->multiInput( $optionKey, $number, 'checkbox' );
	}

	/**
	 * Generate select options
	 *
	 * @param string $optionKey
	 *
	 * @return string
	 */
	protected function selectOption( $optionKey )
	{
		return sprintf( '<option value="%s"%s>%s</option>',
			$optionKey,
			selected( $this->args['value'], $optionKey, false ),
			$this->args['options'][ $optionKey ]
		);
	}

	/**
	 * Generate a single checkbox
	 *
	 * @param string $type
	 *
	 * @return null|string
	 */
	protected function singleInput( $type = 'checkbox' )
	{
		$optionKey = key( $this->args['options'] );

		$args = $this->multiInputArgs( $type, $optionKey, 1 );

		if( !$args )return;

		$atts = $this->normalize();
		$atts = wp_parse_args( $args['attributes'], $atts );

		$attributes = '';

		foreach( $atts as $key => $val ) {
			$attributes .= sprintf( '%s="%s" ', $key, $val );
		}

		return sprintf( '<label for="%s"><input %s%s/> %s</label>',
			$atts['id'],
			$attributes,
			checked( $args['value'], $atts['value'], false ),
			$args['label']
		);
	}
}
