<?php

namespace GeminiLabs\SiteReviews\Shortcodes;

use GeminiLabs\SiteReviews\Database;

abstract class TinymcePopupGenerator
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
	protected $errors = [];

	/**
	 * @var array
	 */
	protected $required = [];

	/**
	 * @return array
	 */
	abstract public function fields();

	/**
	 * @param string $tag
	 * @return static
	 */
	public function register( $tag, array $args )
	{
		$this->tag = $tag;
		$this->properties = wp_parse_args( $args, [
			'btn_close' => esc_html__( 'Close', 'site-reviews' ),
			'btn_okay' => esc_html__( 'Insert Shortcode', 'site-reviews' ),
			'errors' => $this->errors,
			'fields' => $this->getFields(),
			'label' => '['.$tag.']',
			'required' => $this->required,
			'title' => esc_html__( 'Shortcode', 'site-reviews' ),
		]);
		return $this;
	}

	/**
	 * @return array
	 */
	protected function generateFields( array $fields )
	{
		$generatedFields = array_map( function( $field ) {
			if( empty( $field ))return;
			$field = $this->normalize( $field );
			if( !method_exists( $this, $method = 'normalize'.ucfirst( $field['type'] )))return;
			return $this->$method( $field );
		}, $fields );
		return array_values( array_filter( $generatedFields ));
	}

	/**
	 * @return array
	 */
	protected function getFields()
	{
		$fields = $this->generateFields( $this->fields() );
		if( !empty( $this->errors )) {
			$errors = [];
			foreach( $this->required as $name => $alert ) {
				if( false !== array_search( $name, array_column( $fields, 'name' )))continue;
				$errors[] = $this->errors[$name];
			}
			$this->errors = $errors;
		}
		return empty( $this->errors )
			? $fields
			: $this->errors;
	}

	/**
	 * @return array
	 */
	protected function normalize( array $field )
	{
		return wp_parse_args( $field, [
			'items' => [],
			'type' => '',
		]);
	}

	/**
	 * @return void|array
	 */
	protected function normalizeCheckbox( array $field )
	{
		return $this->normalizeField( $field, [
			'checked' => false,
			'label' => '',
			'minHeight' => '',
			'minWidth' => '',
			'name' => false,
			'text' => '',
			'tooltip' => '',
			'type' => '',
			'value' => '',
		]);
	}

	/**
	 * @return void|array
	 */
	protected function normalizeContainer( array $field )
	{
		if( !array_key_exists( 'html', $field ) && !array_key_exists( 'items', $field ))return;
		$field['items'] = $this->generateFields( $field['items'] );
		return $field;
	}

	/**
	 * @return void|array
	 */
	protected function normalizeField( array $field, array $defaults )
	{
		if( !$this->validate( $field ))return;
		return array_filter( shortcode_atts( $defaults, $field ), function( $value ) {
			return $value !== '';
		});
	}

	/**
	 * @return void|array
	 */
	protected function normalizeListbox( array $field )
	{
		$listbox = $this->normalizeField( $field, [
			'label' => '',
			'minWidth' => '',
			'name' => false,
			'options' => [],
			'placeholder' => esc_attr__( '- Select -', 'site-reviews' ),
			'tooltip' => '',
			'type' => '',
			'value' => '',
		]);
		if( !is_array( $listbox ))return;
		if( !array_key_exists( '', $listbox['options'] )) {
			$listbox['options'] = ['' => $listbox['placeholder']] + $listbox['options'];
		}
		foreach( $listbox['options'] as $value => $text ) {
			$listbox['values'][] = [
				'text' => $text,
				'value' => $value,
			];
		}
		return $listbox;
	}

	/**
	 * @return void|array
	 */
	protected function normalizePost( array $field )
	{
		if( !is_array( $field['query_args'] )) {
			$field['query_args'] = [];
		}
		$posts = get_posts( wp_parse_args( $field['query_args'], [
			'order' => 'ASC',
			'orderby' => 'title',
			'post_type' => 'post',
			'posts_per_page' => 30,
		]));
		if( !empty( $posts )) {
			$options = [];
			foreach( $posts as $post ) {
				$options[$post->ID] = esc_html( $post->post_title );
			}
			$field['options'] = $options;
			$field['type'] = 'listbox';
			return $this->normalizeListbox( $field );
		}
		$this->validate( $field );
	}

	/**
	 * @return void|array
	 */
	protected function normalizeTextbox( array $field )
	{
		return $this->normalizeField( $field, [
			'hidden' => false,
			'label' => '',
			'maxLength' => '',
			'minHeight' => '',
			'minWidth' => '',
			'multiline' => false,
			'name' => false,
			'size' => '',
			'text' => '',
			'tooltip' => '',
			'type' => '',
			'value' => '',
		]);
	}

	/**
	 * @return bool
	 */
	protected function validate( array $field )
	{
		$args = shortcode_atts([
			'label' => '',
			'name' => false,
			'required' => false,
		], $field );
		if( !$args['name'] ) {
			return false;
		}
		return $this->validateErrors( $args ) && $this->validateRequired( $args );
	}

	/**
	 * @return bool
	 */
	protected function validateErrors( array $args )
	{
		if( !isset( $args['required']['error'] )) {
			return true;
		}
		$this->errors[$args['name']] = $this->normalizeContainer([
			'html' => $args['required']['error'],
			'type' => 'container',
		]);
		return false;
	}

	/**
	 * @return bool
	 */
	protected function validateRequired( array $args )
	{
		if( $args['required'] == false ) {
			return true;
		}
		$alert = esc_html__( 'Some of the shortcode options are required.', 'site-reviews' );
		if( isset( $args['required']['alert'] )) {
			$alert = $args['required']['alert'];
		}
		else if( !empty( $args['label'] )) {
			$alert = sprintf(
				esc_html_x( 'The "%s" option is required.', 'the option label', 'site-reviews' ),
				str_replace( ':', '', $args['label'] )
			);
		}
		$this->required[$args['name']] = $alert;
		return false;
	}
}
