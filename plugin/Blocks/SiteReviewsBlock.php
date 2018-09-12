<?php

namespace GeminiLabs\SiteReviews\Blocks;

use GeminiLabs\SiteReviews\Blocks\BlockGenerator;

class SiteReviewsBlock extends BlockGenerator
{
	/**
	 * @return array
	 */
	public function attributes()
	{
		return [];
	}

	/**
	 * @return void
	 */
	public function render( array $attributes )
	{
		return print_r( $attributes, 1 );
	}
}



