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
				'submit_button' => $this->buildSubmitButton().$this->buildRecaptcha(),
			],
			'fields' => $this->getFields(),
		]);
	}

	/**
	 * @return void|string
	 */
	protected function buildRecaptcha()
	{
		$integration = glsr( OptionManager::class )->get( 'settings.submissions.recaptcha.integration' );
		$recaptchaMethod = glsr( Helper::class )->buildMethodName( $integration, 'getRecaptcha' );
		if( method_exists( $this, $recaptchaMethod )) {
			return $this->$recaptchaMethod();
		}
	}

	/**
	 * @return string
	 */
	protected function buildResults()
	{
		return glsr( Partial::class )->build( 'form-results', [
			'errors' => $this->errors,
			'message' => $this->message,
		]);
	}

	/**
	 * @return string
	 */
	protected function buildSubmitButton()
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
			'name' => 'nonce',
			'value' => wp_create_nonce( 'submit-review' ),
		],[
			'name' => 'referer',
			'value' => wp_unslash( filter_input( INPUT_SERVER, 'REQUEST_URI' )),
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
	 * @return string
	 */
	protected function getRecaptchaCustom()
	{
		return glsr( Builder::class )->div([
			'class' => 'glsr-recaptcha-holder',
			'data-badge' => sanitize_text_field( glsr( OptionManager::class )->get( 'settings.submissions.recaptcha.position' )),
			'data-sitekey' => sanitize_text_field( glsr( OptionManager::class )->get( 'settings.submissions.recaptcha.key' )),
			'data-size' => 'invisible',
		]);
	}

	/**
	 * @return string
	 */
	protected function getRecaptchaInvisibleRecaptcha()
	{
		ob_start();
		do_action( 'google_invre_render_widget_action' );
		$html = ob_get_clean();
		return glsr( Builder::class )->div( $html, [
			'class' => 'glsr-recaptcha-holder',
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
