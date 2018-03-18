<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Handlers;

use GeminiLabs\SiteReviews\Commands\TogglePinned as Command;
use GeminiLabs\SiteReviews\Notices;

class TogglePinned
{
	/**
	 * @var Notices
	 */
	protected $notices;

	public function __construct( Notices $notices )
	{
		$this->notices = $notices;
	}

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
				? __( 'The review is pinned.', 'site-reviews' )
				: __( 'The review is unpinned.', 'site-reviews' );

			$this->notices->addSuccess( $notice );
		}

		update_post_meta( $command->id, 'pinned', $command->pinned );

		return $command->pinned;
	}
}
