<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Html;

class Normalize
{
	/**
	 * @var array
	 */
	protected $args;

	/**
	 * @var array
	 */
	protected $booleanAttributes;

	/**
	 * @var array
	 */
	protected $formAttributes;

	/**
	 * @var array
	 */
	protected $globalAttributes;

	/**
	 * @var array
	 */
	protected $globalWildcardAttributes;

	/**
	 * @var array
	 */
	protected $inputAttributes;

	/**
	 * @var array
	 */
	protected $inputTypes;

	/**
	 * @var array
	 */
	protected $selectAttributes;

	/**
	 * @var array
	 */
	protected $textareaAttributes;

	public function __construct( array $args = [] )
	{
		$this->args = $args;

		$this->booleanAttributes = apply_filters( 'site-reviews/normalize/boolean/attributes', [
			'autofocus',
			'capture',
			'checked',
			'disabled',
			'draggable',
			'formnovalidate',
			'hidden',
			'multiple',
			'novalidate',
			'readonly',
			'required',
			'selected',
			'spellcheck',
			'webkitdirectory',
		]);

		$this->formAttributes = apply_filters( 'site-reviews/normalize/form/attributes', [
			'accept',
			'accept-charset',
			'action',
			'autocapitalize',
			'autocomplete',
			'enctype',
			'method',
			'name',
			'novalidate',
			'target',
		]);

		$this->globalAttributes = apply_filters( 'site-reviews/normalize/global/attributes', [
			'accesskey',
			'class',
			'contenteditable',
			'contextmenu',
			'dir',
			'draggable',
			'dropzone',
			'hidden',
			'id',
			'lang',
			'spellcheck',
			'style',
			'tabindex',
			'title',
		]);

		$this->globalWildcardAttributes = apply_filters( 'site-reviews/normalize/global/wildcard/attributes', [
			'aria-',
			'data-',
			'item',
			'on',
		]);

		$this->inputAttributes = apply_filters( 'site-reviews/normalize/input/attributes', [
			'accept',
			'autocapitalize',
			'autocomplete',
			'autocorrect',
			'autofocus',
			'capture',
			'checked',
			'disabled',
			'form',
			'formaction',
			'formenctype',
			'formmethod',
			'formnovalidate',
			'formtarget',
			'height',
			'incremental',
			'inputmode',
			'list',
			'max',
			'maxlength',
			'min',
			'minlength',
			'mozactionhint',
			'multiple',
			'name',
			'pattern',
			'placeholder',
			'readonly',
			'required',
			'results',
			'selectionDirection',
			'size',
			'src',
			'step',
			'type',
			'value',
			'webkitdirectory',
			'width',
			'x-moz-errormessage',
		]);

		$this->inputTypes = apply_filters( 'site-reviews/normalize/input/types', [
			'button',
			'checkbox',
			'color',
			'date',
			'datetime',
			'datetime-local',
			'email',
			'file',
			'hidden',
			'image',
			'max',
			'min',
			'month',
			'number',
			'password',
			'radio',
			'range',
			'reset',
			'search',
			'step',
			'submit',
			'tel',
			'text',
			'time',
			'url',
			'value',
			'week',
		]);

		$this->selectAttributes = apply_filters( 'site-reviews/normalize/select/attributes', [
			'autofocus',
			'disabled',
			'form',
			'multiple',
			'name',
			'required',
			'size',
		]);

		$this->textareaAttributes = apply_filters( 'site-reviews/normalize/textarea/attributes', [
			'autocapitalize',
			'autocomplete',
			'autofocus',
			'cols',
			'disabled',
			'form',
			'maxlength',
			'minlength',
			'name',
			'placeholder',
			'readonly',
			'required',
			'rows',
			'selectionDirection',
			'selectionEnd',
			'selectionStart',
			'wrap',
		]);
	}

	/**
	 * Normalize form attributes
	 *
	 * @return array|string
	 */
	public function form( array $args = [], $implode = false )
	{
		$attributes = $this->parseAttributes( $this->formAttributes, $args );

		return $this->maybeImplode( $attributes, $implode );
	}

	/**
	 * Normalize input attributes
	 *
	 * @return array|string
	 */
	public function input( array $args = [], $implode = false )
	{
		$this->filterInputType();

		$attributes = $this->parseAttributes( $this->inputAttributes, $args );

		return $this->maybeImplode( $attributes, $implode );
	}

	/**
	 * Possibly implode attributes into a string
	 *
	 * @param bool|string $implode
	 *
	 * @return array|string
	 */
	public function maybeImplode( array $attributes, $implode = true )
	{
		if( !$implode || $implode !== 'implode' ) {
			return $attributes;
		}

		$results = [];

		foreach( $attributes as $key => $value ) {

			// if data attributes, use single quotes in case of json encoded values
			$quotes = false !== stripos( $key, 'data-' ) ? "'" : '"';

			if( is_array( $value )) {
				$value = json_encode( $value );
				$quotes = "'";
			}

			$results[] = is_string( $key )
				? sprintf( '%1$s=%3$s%2$s%3$s', $key, $value, $quotes )
				: $value;
		}

		return implode( ' ', $results );
	}

	/**
	 * Normalize select attributes
	 *
	 * @return array|string
	 */
	public function select( array $args = [], $implode = false )
	{
		$attributes = $this->parseAttributes( $this->selectAttributes, $args );

		return $this->maybeImplode( $attributes, $implode );
	}

	/**
	 * Normalize textarea attributes
	 *
	 * @return array|string
	 */
	public function textarea( array $args = [], $implode = false )
	{
		$attributes = $this->parseAttributes( $this->textareaAttributes, $args );

		return $this->maybeImplode( $attributes, $implode );
	}

	/**
	 * Filter attributes by the provided attrribute keys and remove any non-boolean keys
	 * with empty values
	 *
	 * @return array
	 */
	protected function filterAttributes( array $attributeKeys )
	{
		$filtered = array_intersect_key( $this->args, array_flip( $attributeKeys ));

		// normalize truthy boolean attributes
		foreach( $filtered as $key => $value ) {
			if( !in_array( $key, $this->booleanAttributes ))continue;

			if( $value !== false ) {
				$filtered[ $key ] = '';
				continue;
			}

			unset( $filtered[ $key ] );
		}

		$filteredKeys = array_filter( array_keys( $filtered ), function( $key ) use ( $filtered ) {
			return !(
				empty( $filtered[ $key ] )
				&& !is_numeric( $filtered[ $key ] )
				&& !in_array( $key, $this->booleanAttributes )
			);
		});

		return array_intersect_key( $filtered, array_flip( $filteredKeys ));
	}

	/**
	 * @return array
	 */
	protected function filterGlobalAttributes()
	{
		$global = $this->filterAttributes( $this->globalAttributes );

		$wildcards = [];

		foreach( $this->globalWildcardAttributes as $wildcard ) {

			foreach( $this->args as $key => $value ) {

				$length = strlen( $wildcard );
				$result = substr( $key, 0, $length) === $wildcard;

				if( $result ) {
					// only allow data attributes to have an empty value
					if( $wildcard != 'data-' && empty( $value ))continue;

					if( is_array( $value )) {

						if( $wildcard != 'data-' )continue;

						$value = json_encode( $value );
					}

					$wildcards[ $key ] = $value;
				}
			}
		}

		return array_merge( $global, $wildcards );
	}

	/**
	 * @return void
	 */
	protected function filterInputType()
	{
		if( !isset( $this->args['type'] ) || !in_array( $this->args['type'], $this->inputTypes )) {
			$this->args['type'] = 'text';
		}
	}

	/**
	 * @return array
	 */
	protected function parseAttributes( array $attributes, array $args = [] )
	{
		if( !empty( $args )) {
			$this->args = array_change_key_case( $args );
		}

		$global = $this->filterGlobalAttributes();
		$local  = $this->filterAttributes( $attributes );

		return array_merge( $global, $local );
	}
}
