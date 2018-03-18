<?php

/**
 * Site Reviews Form shortcode
 *
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Shortcodes;

use GeminiLabs\SiteReviews\Shortcode;
use GeminiLabs\SiteReviews\Traits\SiteReviewsForm as Common;

/**
 * @property public string|bool $id;
 */
class SiteReviewsForm extends Shortcode
{
	use Common;

	/**
	 * @var bool|string
	 */
	public $id = false;

	/**
	 * @return null|string
	 */
	public function printShortcode( $atts = [] )
	{
		$args = $this->normalize( $atts );
		if( $args['assign_to'] == 'post_id' ) {
			$args['assign_to'] = intval( get_the_ID() );
		}
		if( isset( $args['id'] )) {
			$this->id = $args['id'];
		}
		ob_start();
		echo '<div class="glsr-shortcode shortcode-reviews-form">';
		if( !empty( $args['title'] )) {
			printf( '<h3 class="glsr-shortcode-title">%s</h3>', $args['title'] );
		}
		if( !$this->renderRequireLogin() ) {
			echo $this->renderForm( $args );
		}
		echo '</div>';
		return ob_get_clean();
	}
}
