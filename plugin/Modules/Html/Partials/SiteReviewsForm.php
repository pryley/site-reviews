<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Partials;

use GeminiLabs\SiteReviews\Modules\Html;
use GeminiLabs\SiteReviews\Modules\Html\Builder;

class SiteReviewsForm
{
	/**
	 * @return void|string
	 */
	public function build( array $args = [] )
	{
		$partial = glsr( Html::class )->buildTemplate( 'templates/reviews-form', [
			'globals' => $args,
		]);
		$form = glsr( Builder::class )->form( $partial, [
			'class' => 'glsr-form',
			'id' => $args['id'],
			'method' => 'post',
		]);
		return glsr( Builder::class )->div( $form, [
			'class' => 'glsr-form-wrap '.$args['class'],
		]);
	}
}
