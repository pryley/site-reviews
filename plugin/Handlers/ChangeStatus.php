<?php

namespace GeminiLabs\SiteReviews\Handlers;

use GeminiLabs\SiteReviews\Commands\ChangeStatus as Command;
use GeminiLabs\SiteReviews\Modules\Html\Builder;

class ChangeStatus
{
	/**
	 * @return array
	 */
	public function handle( Command $command )
	{
		$postId = wp_update_post([
			'ID' => $command->id,
			'post_status' => $command->status,
		]);
		return [
			'class' => 'status-'.$command->status,
			'link' => $this->getPostLink( $postId ).$this->getPostState( $postId ),
		];
	}

	/**
	 * @param int $postId
	 * @return string
	 */
	protected function getPostLink( $postId )
	{
		$title = _draft_or_post_title( $postId );
		return glsr( Builder::class )->a( $title, [
			'href' => get_edit_post_link( $postId ),
			'class' => 'row-title',
			'title' => __( 'Edit', 'site-reviews' ).' &#8220;'.esc_attr( $title ).'&#8221;',
		]);
	}

	/**
	 * @param int $postId
	 * @return string
	 */
	protected function getPostState( $postId )
	{
		ob_start();
		_post_states( get_post( $postId ));
		return ob_get_clean();
	}
}
