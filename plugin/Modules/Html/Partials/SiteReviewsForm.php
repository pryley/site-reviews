<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Partials;

use GeminiLabs\SiteReviews\Modules\Html;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\Field;
use GeminiLabs\SiteReviews\Modules\Html\Form;
use GeminiLabs\SiteReviews\Modules\Html\Partial;
use GeminiLabs\SiteReviews\Modules\Html\Template;

class SiteReviewsForm
{
	/**
	 * @var array
	 */
	protected $args;

	/**
	 * @return void|string
	 */
	public function build( array $args = [] )
	{
		$this->args = $args;
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
		return glsr( Partial::class )->build( 'form-results' );
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
			[$this->getHoneypotField()],
			glsr( Form::class )->getFields( 'submission-form' ),
			$this->getHiddenFields()
		);
		glsr_debug( $fields );
		return $fields;
	}

	/**
	 * @return array
	 */
	protected function getHiddenFields()
	{
		$fields = [[
			'name' => 'assign_to',
			'value' => $this->args['assign_to'],
		],[
			'name' => 'category',
			'value' => $this->args['category'],
		],[
			'name' => 'excluded',
			'value' => $this->args['excluded'],
		],[
			'name' => 'id',
			'value' => $this->args['id'],
		],[
			'name' => '_wp_http_referer',
			'value' => wp_get_referer(),
		],[
			'name' => '_wpnonce',
			'value' => wp_create_nonce( 'post-review' ),
		]];
		return array_values( array_map( function( $field ) {
			return new Field( wp_parse_args( $field, ['type' => 'hidden'] ));
		}, $fields ));
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
}
