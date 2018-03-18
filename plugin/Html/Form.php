<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Html;

use GeminiLabs\SiteReviews\App;

use Exception;

class Form
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
	protected $customFields;

	/**
	 * @var array
	 */
	protected $dependencies;

	/**
	 * @var array
	 */
	protected $fields;

	public function __construct( App $app )
	{
		$this->app          = $app;
		$this->args         = [];
		$this->customFields = [];
		$this->dependencies = [];
		$this->fields       = [];
	}

	/**
	 * @param string $property
	 * @return mixed
	 * @throws Exception
	 */
	public function __get( $property )
	{
		if( in_array( $property, [
			'args',
			'customFields',
			'dependencies',
			'fields',
		])) {
			return $this->$property;
		}
		throw new Exception( 'Invalid ' . __CLASS__ . ' property: ' . $property );
	}

	/**
	 * @param string $property
	 * @param string $value
	 *
	 * @return void
	 * @throws Exception
	 */
	public function __set( $property, $value )
	{
		switch( $property ) {
			case 'args':
			case 'customFields':
			case 'dependencies':
			case 'fields':
				$this->$property = $value;
				break;
			default:
				throw new Exception( 'Invalid ' . __CLASS__ . ' property: ' . $property );
		}
	}

	/**
	 * Add a field to the form
	 *
	 * @return Form
	 */
	public function addCustomField( callable $callback )
	{
		$this->customFields[] = $callback();
		return $this;
	}

	/**
	 * Add a field to the form
	 *
	 * @return Form
	 */
	public function addField( array $args = [] )
	{
		$field = $this->app->make( 'Html\Field' )->normalize( $args );

		if( $field->args['render'] !== false ) {
			$this->dependencies = array_unique(
				array_merge( $field->dependencies, $this->dependencies )
			);
			$this->fields[] = $field;
		}

		return $this;
	}

	/**
	 * Normalize the form arguments
	 *
	 * @return Form
	 */
	public function normalize( array $args = [] )
	{
		$defaults = [
			'action'     => '',
			'attributes' => '',
			'id'         => '',
			'class'      => '',
			'enctype'    => 'multipart/form-data',
			'method'     => 'post',
			'nonce'      => '',
			'submit'     => __( 'Submit', 'site-reviews' ),
		];

		$this->args = array_merge( $defaults, $args );

		$attributes = $this->app->make( 'Html\Normalize' )->form( $this->args, 'implode' );

		$this->args['attributes'] = $attributes;

		return $this;
	}

	/**
	 * Render the form
	 * @return string
	 */
	public function render()
	{
		return sprintf( '<form %s>%s</form>',
			$this->args['attributes'],
			$this->generateFields() . $this->generateCustomFields() . $this->generateSubmitButton()
		);
	}

	/**
	 * Reset the Form
	 *
	 * @return Form
	 */
	public function reset()
	{
		$this->args         = [];
		$this->dependencies = [];
		$this->fields       = [];

		return $this;
	}

	/**
	 * Generate the hidden fields of a wp-admin form
	 *
	 * @return string
	 */
	protected function generateAdminFields()
	{
		ob_start();

		settings_fields( $this->args['nonce'] );
		do_settings_sections( $this->args['nonce'] );

		return ob_get_clean();
	}

	/**
	 * Generate any custom fields of a front-end form
	 *
	 * @return null|string
	 */
	protected function generateCustomFields()
	{
		return implode( '', $this->customFields );
	}

	/**
	 * Generate the form fields
	 *
	 * @return string
	 */
	protected function generateFields()
	{
		$hiddenFields = '';

		$fields = array_reduce( $this->fields, function( $carry, $formField ) use ( &$hiddenFields ) {

			$stringLegend    = '<legend class="screen-reader-text"><span>%s</span></legend>';
			$stringFieldset  = '<fieldset%s>%s%s</fieldset>';
			$stringRendered  = '<tr class="glsr-field %s"><th scope="row">%s</th><td>%s</td></tr>';
			$outsideRendered = '</tbody></table>%s<table class="form-table"><tbody>';

			// set field value only when rendering because we need to check the default setting
			// against the database
			$field = $formField->setValue()->getField();

			$multi    = $field->multi === true;
			$label    = $field->generateLabel();
			$rendered = $field->render();

			// render hidden inputs outside the table
			if( $field->args['type'] === 'hidden' ) {
				$hiddenFields .= $rendered;
				return $carry;
			}

			$hiddenClass = $this->isFieldHidden( $formField ) ? 'hidden' : '';

			if( $multi ) {
				if( $depends = $formField->getDataDepends() ) {
					$depends = sprintf( ' data-depends=\'%s\'', json_encode( $depends ));
				}

				$legend = $label ? sprintf( $stringLegend, $label ) : '';
				$rendered = sprintf( $stringFieldset, $depends, $legend, $rendered );
			}

			$renderedField = $field->outside
				? sprintf( $outsideRendered, $rendered )
				: sprintf( $stringRendered, $hiddenClass, $label, $rendered );

			return $carry . $renderedField;
		});

		$hiddenFields .= is_admin()
			? $this->generateAdminFields()
			: $this->generatePublicFields();

		if( empty( $fields )) {
			return $hiddenFields;
		}

		return sprintf( '<table class="form-table"><tbody>%s</tbody></table>%s', $fields, $hiddenFields );
	}

	/**
	 * Generate the hidden fields of a front-end form
	 *
	 * @return null|string
	 */
	protected function generatePublicFields()
	{
	}

	/**
	 * Generate the form submit button
	 *
	 * @return null|string
	 */
	protected function generateSubmitButton()
	{
		$args = $this->args['submit'];

		is_array( $args ) ?: $args = ['text' => $args ];

		$args = shortcode_atts([
			'text' => __( 'Save Changes', 'site-reviews' ),
			'type' => 'primary',
			'name' => 'submit',
			'wrap' => true,
			'other_attributes' => null,
		], $args );

		if( is_admin() ) {
			ob_start();
			submit_button( $args['text'], $args['type'], $args['name'], $args['wrap'], $args['other_attributes'] );
			return ob_get_clean();
		}
	}

	/**
	 * @param object $field GeminiLabs\SiteReviews\Html\Fields\*
	 *
	 * @return bool|null
	 */
	protected function isFieldHidden( $field )
	{
		if( !( $dependsOn = $field->getDataDepends() ))return;

		foreach( $this->fields as $formField ) {
			if( $dependsOn['name'] !== $formField->args['name'] )continue;

			if( is_array( $dependsOn['value'] )) {
				return !in_array( $formField->args['value'], $dependsOn['value'] );
			}

			return $dependsOn['value'] != $formField->args['value'];
		}
	}
}
