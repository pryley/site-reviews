<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Handlers;

use GeminiLabs\SiteReviews\App;
use GeminiLabs\SiteReviews\Commands\RegisterPointers as Command;

class RegisterPointers
{
	protected $app;

	public function __construct( App $app )
	{
		$this->app = $app;
	}

	/**
	 * Add pointers to the current screen if they have not yet been dismissed
	 *
	 * @return void
	 */
	public function handle( Command $command )
	{
		// Get dismissed pointers
		$dismissed = get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true );
		$dismissed = explode( ',', (string) $dismissed );

		$pointers = [];

		foreach( $command->pointers as $pointer ) {

			if( $pointer['screen'] != glsr_current_screen()->id || in_array( $pointer['id'], $dismissed ))continue;

			$pointers[] = [
				'id'      => $pointer['id'],
				'screen'  => $pointer['screen'],
				'target'  => $pointer['target'],
				'options' => [
					'content'  => sprintf( '<h3>%s</h3><p>%s</p>', $pointer['title'], $pointer['content'] ),
					'position' => $pointer['position'],
				],
			];
		}

		wp_localize_script( $this->app->id, 'site_reviews_pointers', [
			'pointers' => $pointers,
		]);

		if( empty( $pointers ))return;

		wp_enqueue_style( 'wp-pointer' );
		wp_enqueue_script( 'wp-pointer' );
	}
}
