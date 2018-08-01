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
		do_action( 'site-reviews/local/review/submitted', $review );
		if( $command->ajax_request )return;
		if( empty( $command->referer )) {
			glsr_log()->warning( 'The form referer ($_SERVER[REQUEST_URI]) was empty.' )->info( $command );
			$command->referer = home_url();
		}
		wp_safe_redirect( $command->referer );
		exit;
	}
}
