<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Partials;

use GeminiLabs\SiteReviews\Contracts\PartialContract;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Session;

class FormResults implements PartialContract
{
	/**
	 * @var array
	 */
	protected $errors;

	/**
	 * @return void|string
	 */
	public function build( array $args = [] )
	{
		$this->errors = $args['errors'];
		return glsr( Builder::class )->div( wpautop( $args['message'] ), [
			'class' => $this->getClass(),
		]);
	}

	/**
	 * @return string
	 */
	protected function getClass()
	{
		$errorClass = !empty( $this->errors )
			? 'glsr-has-errors'
			: '';
		return trim( 'glsr-form-messages '.$errorClass );
	}
}
