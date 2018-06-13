<?php

namespace GeminiLabs\SiteReviews\Handlers;

use GeminiLabs\SiteReviews\Commands\TogglePinned as Command;
use GeminiLabs\SiteReviews\Modules\Notice;

class TogglePinned
{
	/**
	 * @return bool
	 */
	public function handle( Command $command )
	{
		if( !get_post( $command->id )) {
			return false;
		}
		if( is_null( $command->pinned )) {
			$meta = get_post_meta( $command->id, 'pinned', true );
			$command->pinned = !wp_validate_boolean( $meta );
		}
		else {
			$notice = $command->pinned
				? __( 'Review pinned.', 'site-reviews' )
				: __( 'Review unpinned.', 'site-reviews' );
			glsr( Notice::class )->addSuccess( $notice );
		}
		update_post_meta( $command->id, 'pinned', $command->pinned );
		return $command->pinned;
	}
}
