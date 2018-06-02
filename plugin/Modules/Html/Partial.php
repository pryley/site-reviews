<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Helper;

class Partial
{
	/**
	 * @param string $partialPath
	 * @return string
	 */
	public function build( $partialPath, array $args = [] )
	{
		$className = glsr( Helper::class )->buildClassName( $partialPath, 'Modules\Html\Partials' );
		if( !class_exists( $className )) {
			glsr_log()->error( 'Partial missing: '.$className );
			return;
		}
		$partial = glsr( $className )->build( $args );
		return apply_filters( 'site-reviews/rendered/partial', $partial, $partialPath, $args );
	}

	/**
	 * @param string $partialPath
	 * @return void
	 */
	public function render( $partialPath, array $args = [] )
	{
		echo $this->build( $partialPath, $args );
	}
}
