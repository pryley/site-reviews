<?php

namespace GeminiLabs\SiteReviews\Handlers;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Commands\RegisterPointers as Command;

class RegisterPointers
{
	/**
	 * Add pointers to the current screen if they have not yet been dismissed
	 * @return void
	 */
	public function handle( Command $command )
	{
		$pointers = $this->generatePointers( $command->pointers );
		wp_localize_script( Application::ID, 'site_reviews_pointers', [
			'pointers' => $pointers,
		]);
		if( empty( $pointers ))return;
		wp_enqueue_style( 'wp-pointer' );
		wp_enqueue_script( 'wp-pointer' );
	}

	/**
	 * @return array
	 */
	public function generatePointers( array $pointers )
	{
		$dismissedPointers = get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true );
		$dismissedPointers = explode( ',', (string)$dismissedPointers );
		$generatedPointers = [];
		foreach( $pointers as $pointer ) {
			if( $pointer['screen'] != glsr_current_screen()->id )continue;
			if( in_array( $pointer['id'], $dismissedPointers ))continue;
			$generatedPointers[] = $this->generatePointer( $pointer );
		}
		return $generatedPointers;
	}

	/**
	 * @return array
	 */
	public function generatePointer( array $pointer )
	{
		return [
			'id' => $pointer['id'],
			'options' => [
				'content' => '<h3>'.$pointer['title'].'</h3><p>'.$pointer['content'].'</p>',
				'position' => $pointer['position'],
			],
			'screen' => $pointer['screen'],
			'target' => $pointer['target'],
		];
	}
}
