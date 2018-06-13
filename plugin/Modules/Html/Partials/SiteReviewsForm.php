<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Partials;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Html;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\Field;
use GeminiLabs\SiteReviews\Modules\Html\Form;
use GeminiLabs\SiteReviews\Modules\Html\Partial;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Modules\Session;

class SiteReviewsForm
{
	/**
	 * @var array
	 */
	protected $args;

	/**
	 * @var array
	 */
	protected $errors;

	/**
	 * @var array
	 */
	protected $message;

	/**
	 * @var array
	 */
	protected $required;

	/**
	 * @var array
	 */
	protected $values;

	/**
	 * @return void|string
	 */
	public function build( array $args = [] )
	{
		$this->args = $args;
		$this->errors = glsr( Session::class )->get( $args['id'].'errors', [], true );
		$this->message = glsr( Session::class )->get( $args['id'].'message', [], true );
		$this->required = glsr( OptionManager::class )->get( 'settings.submissions.required', [] );
		$this->values = glsr( Session::class )->get( $args['id'].'values', [], true );
		return glsr( Template::class )->build( 'templates/reviews-form', [
			'context' => [
				'class' => $this->getClass(),
				'id' => $this->args['id'],
				'results' => $this->buildResults(),
				'submit_button' => $this->buildSubmitButton(),
			],
			'fields' => $this->getFields(),
		]);
	}

	/**
	 * @return string
	 */
	public function buildResults()
	{
		return glsr( Partial::class )->build( 'form-results', [
			'errors' => $this->errors,
			'message' => $this->message,
		]);
	}

	/**
	 * @return string
	 */
	public function buildSubmitButton()
	{
		return glsr( Builder::class )->button( '<span></span>'.__( 'Submit your review', 'site-reviews' ), [
			'type' => 'submit',
		]);
	}

	/**
	 * @return string
	 */
	protected function getClass()
	{
		$style = apply_filters( 'site-reviews/reviews-form/style', 'glsr-style' );
		return trim( 'glsr-form '.$style.' '.$this->args['class'] );
	}

	/**
	 * @return array
	 */
	protected function getFields()
	{
		$fields = array_merge(
			$this->getHiddenFields(),
			[$this->getHoneypotField()],
			$this->normalizeFields( glsr( Form::class )->getFields( 'submission-form' ))
		);
		// glsr_debug( $fields );
		return $fields;
	}

	/**
	 * @return array
	 */
	protected function getHiddenFields()
	{
		$fields = [[
			'name' => 'action',
			'value' => 'submit-review',
		],[
			'name' => 'assign_to',
			'value' => $this->args['assign_to'],
		],[
			'name' => 'category',
			'value' => $this->args['category'],
		],[
			'name' => 'excluded',
			'value' => $this->args['excluded'], // @todo should default to "[]"
		],[
			'name' => 'form_id',
			'value' => $this->args['id'],
		],[
			'name' => '_wp_http_referer',
			'value' => wp_unslash( filter_input( INPUT_SERVER, 'REQUEST_URI' )), // @todo this doesn't work, maybe need to get this on submit
		],[
			'name' => '_wpnonce',
			'value' => wp_create_nonce( 'submit-review' ),
		]];
		return array_map( function( $field ) {
			return new Field( wp_parse_args( $field, ['type' => 'hidden'] ));
		}, $fields );
	}

	/**
	 * @return Field
	 */
	protected function getHoneypotField()
	{
		return new Field([
			'name' => 'gotcha',
			'type' => 'honeypot',
		]);
	}

	/**
	 * @return void
	 */
	protected function normalizeFieldErrors( Field &$field )
	{
		if( !array_key_exists( $field->field['path'], $this->errors ))return;
		$field->field['errors'] = $this->errors[$field->field['path']];
	}

	/**
	 * @return void
	 */
	protected function normalizeFieldRequired( Field &$field )
	{
		if( !in_array( $field->field['path'], $this->required ))return;
		$field->field['required'] = true;
	}

	/**
	 * @return array
	 */
	protected function normalizeFields( $fields )
	{
		foreach( $fields as &$field ) {
			$this->normalizeFieldErrors( $field );
			$this->normalizeFieldRequired( $field );
			$this->normalizeFieldValue( $field );
		}
		return $fields;
	}

	/**
	 * @return void
	 */
	protected function normalizeFieldValue( Field &$field )
	{
		if( !array_key_exists( $field->field['path'], $this->values ))return;
		if( in_array( $field->field['type'], ['radio', 'checkbox'] )) {
			$field->field['checked'] = $field->field['value'] == $this->values[$field->field['path']];
		}
		else {
			$field->field['value'] = $this->values[$field->field['path']];
		}
	}
}
