<?php

/**
 * Shortcode MCE Dialog Generator
 *
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2017, Paul Ryley
 * @license   GPLv3
 * @since     2.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Shortcodes\Buttons;

abstract class Generator
{
	/**
	 * @var array
	 */
	public $properties;

	/**
	 * @var string
	 */
	public $tag;

	/**
	 * @var array
	 */
	protected $errors;

	/**
	 * @var array
	 */
	protected $required;

	/**
	 * @return array
	 */
	abstract public function fields();

	/**
	 * @param string $tag
	 *
	 * @return self
	 */
	public function register( $tag, array $args )
	{
		$this->errors   = [];
		$this->required = [];
		$this->tag      = $tag;

		// Generate the fields, errors, and requirements
		$defaults = [
			'btn_close' => esc_html__( 'Close', 'site-reviews' ),
			'btn_okay'  => esc_html__( 'Insert Shortcode', 'site-reviews' ),
			'errors'    => $this->errors,
			'fields'    => $this->getFields(),
			'label'     => '[' . $tag . ']',
			'required'  => $this->required,
			'title'     => esc_html__( 'Shortcode', 'site-reviews' ),
		];

		$this->properties = wp_parse_args( $args, $defaults );

		return $this;
	}

	/**
	 * Get the generated shortcode dialog fields
	 *
	 * @return array
	 */
	protected function getFields()
	{
		$errors = [];
		$fields = $this->setFields( $this->fields() );

		if( !empty( $this->errors )) {

			foreach( $this->required as $name => $alert ) {
				if( false === array_search( $name, array_column( $fields, 'name' )) ) {
					$errors[] = $this->errors[ $name ];
				}
			}

			$this->errors = $errors;
		}

		return empty( $errors )
			? $fields
			: $errors;
	}

	/**
	 * Generate the shortcode dialog fields
	 *
	 * @param array $defined_fields
	 *
	 * @return array|null
	 */
	protected function setFields( $defined_fields )
	{
		if( !is_array( $defined_fields ))return;

		$fields = [];

		foreach( $defined_fields as $field ) {

			$defaults = [
				'label'       => false,
				'name'        => false,
				'options'     => [],
				'placeholder' => false,
				'tooltip'     => false,
				'type'        => '',
			];

			$field  = wp_parse_args( (array) $field, $defaults );
			$method = glsr_resolve( 'Helper' )->buildMethodName( $field['type'], 'generate' );

			if( method_exists( $this, $method )) {

				$field = call_user_func( array( $this, $method ), $field );

				if( $field ) {
					$fields[] = $field;
				}
			}
		}

		return $fields;
	}

	/**
	 * Generate a TinyMCE checkbox field
	 *
	 * @param array $field
	 *
	 * @return null|array
	 */
	protected function generateCheckbox( $field )
	{
		if( !$this->validate( $field ))return;

		$textbox = shortcode_atts( array(
			'checked'   => false,
			'label'     => '',
			'minHeight' => '',
			'minWidth'  => '',
			'name'      => false,
			'text'      => '',
			'tooltip'   => '',
			'type'      => '',
			'value'     => '',
		), $field );

		return array_filter( $textbox, function( $value ) {
			return $value !== '';
		});
	}

	/**
	 * Generate a TinyMCE container field
	 *
	 * @param array $field
	 *
	 * @return array|null
	 */
	protected function generateContainer( $field )
	{
		if( !array_key_exists( 'html', $field ) && !array_key_exists( 'items', $field ))return;

		if( isset( $field['items'] ) && is_array( $field['items'] )) {
			$field['items'] = $this->setFields( $field['items'] );
		}

		return $field;
	}

	/**
	 * Generate a TinyMCE listbox field
	 *
	 * @param array $field
	 *
	 * @return array|null
	 */
	protected function generateListbox( $field )
	{
		if( !$this->validate( $field ))return;

		$args = shortcode_atts([
			'label'    => '',
			'minWidth' => '',
			'name'     => false,
			'tooltip'  => '',
			'type'     => '',
			'value'    => '',
		], $field );

		$listbox = [];

		foreach( $args as $key => $value ) {
			if( !$value )continue;
			$listbox[ $key ] = $value;
		}

		$field['placeholder'] ?: $field['placeholder'] = esc_attr__( '- Select -', 'site-reviews' );
		$field['options'] = ['' => $field['placeholder']] + $field['options'];

		foreach( $field['options'] as $value => $text ) {
			$listbox['values'][] = [
				'text'  => $text,
				'value' => $value,
			];
		}

		return $listbox;
	}

	/**
	 * Generate a TinyMCE listbox field for a post_type
	 *
	 * @param array $field
	 *
	 * @return null|array|false
	 */
	protected function generatePost( $field )
	{
		$defaults = [
			'order'          => 'ASC',
			'orderby'        => 'title',
			'post_type'      => 'post',
			'posts_per_page' => 30,
		];

		is_array( $field['query_args'] ) ?: $field['query_args'] = [];

		$args  = wp_parse_args( $field['query_args'], $defaults );
		$posts = get_posts( $args );

		if( is_array( $posts )) {
			$options = [];

			foreach( $posts as $post ) {
				$options[ absint( $post->ID ) ] = esc_html( $post->post_title );
			}

			$field['type'] = 'listbox';
			$field['options'] = $options;

			return $this->generateListbox( $field );
		}

		// perform validation here before returning false
		$this->validate( $field );

		return false;
	}

	/**
	 * Generate a TinyMCE textbox field
	 *
	 * @param array $field
	 *
	 * @return null|array
	 */
	protected function generateTextbox( $field )
	{
		if( !$this->validate( $field ))return;

		$textbox = shortcode_atts([
			'hidden'    => false,
			'label'     => '',
			'maxLength' => '',
			'minHeight' => '',
			'minWidth'  => '',
			'multiline' => false,
			'name'      => false,
			'size'      => '',
			'text'      => '',
			'tooltip'   => '',
			'type'      => '',
			'value'     => '',
		], $field );

		return array_filter( $textbox, function( $value ) {
			return $value !== '';
		});
	}

	/**
	 * Perform validation for a single field
	 *
	 * Returns true or false depending on whether the field has a 'name' attribute.
	 * This method also populates the shortcode's $errors and $required arrays.
	 *
	 * @param array $field
	 *
	 * @return bool
	 */
	protected function validate( $field )
	{
		$vars = shortcode_atts([
			'name'     => false,
			'required' => false,
			'label'    => '',
		], $field );

		if( !$vars['name'] ) {
			return false;
		}

		extract( $vars );

		if( isset( $required['error'] )) {
			$error = [
				'type' => 'container',
				'html' => $required['error'],
			];

			$this->errors[ $name ] = $this->generateContainer( $error );
		}

		if( !!$required || is_array( $required )) {

			$alert = esc_html__( 'Some of the shortcode options are required.', 'site-reviews' );

			if( isset( $required['alert'] )) {
				$alert = $required['alert'];
			}
			else if( !empty( $label )) {
				$alert = sprintf(
					esc_html_x( 'The "%s" option is required.', 'the option label', 'site-reviews' ),
					str_replace( ':', '', $label )
				);
			}

			$this->required[ $name ] = $alert;
		}

		return true;
	}
}
