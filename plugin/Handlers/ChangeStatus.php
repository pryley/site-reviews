<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2017, Paul Ryley
 * @license   GPLv3
 * @since     2.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Handlers;

use GeminiLabs\SiteReviews\Commands\ChangeStatus as Command;

class ChangeStatus
{
	/**
	 * @return array
	 */
	public function handle( Command $command )
	{
		$postId = wp_update_post([
			'ID'          => $command->id,
			'post_status' => $command->status,
		]);

		$title = _draft_or_post_title( $postId );

		$link = sprintf( '<a class="row-title" href="%s" title="%s">%s</a>',
			get_edit_post_link( $postId ),
			esc_attr( sprintf( '%s &#8220;%s&#8221;', __( 'Edit', 'site-reviews' ), $title )),
			$title
		);

		ob_start();
			_post_states( get_post( $postId ));
		$state = ob_get_clean();

		return [
			'class' => sprintf( 'status-%s', $command->status ),
			'link'  => $link . $state,
		];
	}
}
