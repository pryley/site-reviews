<?php

namespace GeminiLabs\SiteReviews\Controllers\EditorController;

use GeminiLabs\SiteReviews\Database\CountsManager;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Helper;

class Metaboxes
{
	/**
	 * @param int $postId
	 * @return void
	 */
	public function saveAssignedToMetabox( $postId )
	{
		if( !wp_verify_nonce( glsr( Helper::class )->filterInput( '_nonce-assigned-to' ), 'assigned_to' ))return;
		$assignedTo = strval( glsr( Helper::class )->filterInput( 'assigned_to' ));
		if( get_post_meta( $postId, 'assigned_to', true ) != $assignedTo ) {
			$review = glsr( ReviewManager::class )->single( get_post( $postId ));
			glsr( CountsManager::class )->decreasePostCounts( $review );
		}
		update_post_meta( $postId, 'assigned_to', $assignedTo );
	}

	/**
	 * @param int $postId
	 * @return mixed
	 */
	public function saveResponseMetabox( $postId )
	{
		if( !wp_verify_nonce( glsr( Helper::class )->filterInput( '_nonce-response' ), 'response' ))return;
		$response = strval( glsr( Helper::class )->filterInput( 'response' ));
		update_post_meta( $postId, 'response', trim( wp_kses( $response, [
			'a' => ['href' => [], 'title' => []],
			'em' => [],
			'strong' => [],
		])));
	}
}
