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

class Tabs extends Base
{
	/**
	 * Generate tabs
	 *
	 * @return null|string
	 */
	public function render()
	{
		$defaults = [
			'page' => '',
			'tabs' => [],
			'tab'  => '',
		];

		$args = shortcode_atts( $defaults, $this->args );

		extract( $args );

		if( count( $tabs ) < 2 )return;

		$links = array_reduce( array_keys( $tabs ),
			function( $result, $key ) use ( $page, $tabs, $tab ) {
			return $result . sprintf( '<a href="?post_type=site-review&page=%s&tab=%s" class="nav-tab%s">%s</a>',
				$page,
				$key,
				(0 === strpos( $tab, $key )) ? ' nav-tab-active' : null,
				$tabs[ $key ]['title']
			);
		});

		return sprintf( '<h2 class="nav-tab-wrapper">%s</h2>', $links );
	}
}
