<?php

namespace GeminiLabs\SiteReviews\Handlers;

use GeminiLabs\SiteReviews\Commands\CreateReview as Command;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Modules\Notification;
use GeminiLabs\SiteReviews\Modules\Session;

class CreateReview
{
	/**
	 * @return void|string
	 */
	public function handle( Command $command )
	{
		$review = glsr( ReviewManager::class )->create( $command );
		if( !$review ) {
			glsr( Session::class )->set( $command->form_id.'errors', [] );
			glsr( Session::class )->set( $command->form_id.'message', __( 'Your review could not be submitted and the error has been logged. Please notify the site admin.', 'site-reviews' ));
			return;
		}
		glsr( Session::class )->set( $command->form_id.'message', __( 'Your review has been submitted!', 'site-reviews' ));
		glsr( Notification::class )->send( $review );
		if( $command->ajax_request )return;
		wp_safe_redirect( $this->getReferer( $command ));
		exit;
	}

	/**
	 * @return string
	 */
	protected function getReferer( Command $command )
	{
		$referer = trim( strval( get_post_meta( $command->post_id, 'redirect_to', true )));
		if( empty( $referer )) {
			$referer = $command->referer;
		}
		if( empty( $referer )) {
			glsr_log()->warning( 'The form referer ($_SERVER[REQUEST_URI]) was empty.' )->info( $command );
			$referer = home_url();
		}
		return $referer;
	}
}
