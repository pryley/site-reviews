<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Html\Partials;

use GeminiLabs\SiteReviews\Html\Partials\Base;

class Subsubsub extends Base
{
	/**
	 * Generate tabs
	 *
	 * @return null|string
	 */
	public function render()
	{
		$defaults = [
			'page'    => '',
			'tabs'    => [],
			'tab'     => '',
			'section' => '',
		];

		$args = shortcode_atts( $defaults, $this->args );

		extract( $args );

		if( !isset( $tabs[ $tab ]['sections'] ) || count( $tabs[ $tab ]['sections'] ) < 2 )return;

		$linkEls = array_reduce( array_keys( $tabs[ $tab ]['sections'] ),
			function( $result, $key ) use ( $page, $tabs, $tab, $section ) {

			$sections = $tabs[ $tab ]['sections'];

			return $result . sprintf( '<li><a href="?post_type=site-review&page=%s&tab=%s&section=%s"%s>%s</a>%s</li>',
				$page,
				$tab,
				$key,
				$section == $key ? ' class="current"' : null,
				$sections[ $key ],
				end( $sections ) !== $sections[ $key ] ? ' | ' : ''
			);
		});

		return sprintf( '<ul class="subsubsub glsr-subsubsub">%s</ul>', $linkEls );
	}
}
