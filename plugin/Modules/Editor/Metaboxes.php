<?php

namespace GeminiLabs\SiteReviews\Modules\Editor;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Rating;
use WP_Post;

class Metaboxes
{
	const META_AVERAGE = '_glsr_average';
	const META_RANKING = '_glsr_ranking';
	const META_REVIEW_ID = '_glsr_review_id';

	/**
	 * @param array $postData
	 * @param array $meta
	 * @param int $postId
	 * @return void
	 */
	public function onCreateReview( $postData, $meta, $postId )
	{
		if( !$this->isReviewPostType( $review = get_post( $postId )))return;
		$this->updateAssignedToPost( $review );
	}

	/**
	 * @param int $postId
	 * @return void
	 */
	public function onDeleteReview( $postId )
	{
		if( !$this->isReviewPostType( $review = get_post( $postId )))return;
		$review->post_status = 'deleted'; // important to change the post_status here first!
		$this->updateAssignedToPost( $review );
	}

	/**
	 * @param int $postId
	 * @return void
	 */
	public function onSaveReview( $postId, WP_Post $review )
	{
		$this->updateAssignedToPost( $review );
	}

	/**
	 * @param int $postId
	 * @return void
	 */
	public function saveAssignedToMetabox( $postId )
	{
		if( !wp_verify_nonce( filter_input( INPUT_POST, '_nonce-assigned-to' ), 'assigned_to' ))return;
		$assignedTo = filter_input( INPUT_POST, 'assigned_to' );
		$assignedTo || $assignedTo = '';
		if( get_post_meta( $postId, 'assigned_to', true ) != $assignedTo ) {
			$this->onDeleteReview( $postId );
		}
		update_post_meta( $postId, 'assigned_to', $assignedTo );
	}

	/**
	 * @param int $postId
	 * @return mixed
	 */
	public function saveResponseMetabox( $postId )
	{
		if( !wp_verify_nonce( filter_input( INPUT_POST, '_nonce-response' ), 'response' ))return;
		$response = filter_input( INPUT_POST, 'response' );
		$response || $response = '';
		update_post_meta( $postId, 'response', trim( wp_kses( $response, [
			'a' => ['href' => [], 'title' => []],
			'em' => [],
			'strong' => [],
		])));
	}

	/**
	 * @param int $postId
	 * @return int|false
	 */
	protected function getAssignedToPostId( $postId )
	{
		$assignedTo = intval( get_post_meta( $postId, 'assigned_to', true ));
		if(( $post = get_post( $assignedTo )) instanceof WP_Post ) {
			return $post->ID;
		}
		return false;
	}

	/**
	 * @param mixed $post
	 * @return bool
	 */
	protected function isReviewPostType( $post )
	{
		return $post instanceof WP_Post && $post->post_type == Application::POST_TYPE;
	}

	/**
	 * @return int
	 */
	protected function recalculatePostAverage( array $reviews )
	{
		return glsr( Rating::class )->getAverage( $reviews );
	}

	/**
	 * @return int
	 */
	protected function recalculatePostRanking( array $reviews )
	{
		return glsr( Rating::class )->getRanking( $reviews );
	}

	/**
	 * @return void
	 */
	protected function updateAssignedToPost( WP_Post $review )
	{
		if( !( $postId = $this->getAssignedToPostId( $review->ID )))return;
		$reviewIds = array_filter( (array)get_post_meta( $postId, static::META_REVIEW_ID ));
		if( empty( $reviewIds ))return;
		$this->updateReviewIdOfPost( $postId, $review, $reviewIds );
		$updatedReviewIds = array_filter( (array)get_post_meta( $postId, static::META_REVIEW_ID ));
		if( empty( $updatedReviewIds )) {
			delete_post_meta( $postId, static::META_RANKING );
			delete_post_meta( $postId, static::META_REVIEW_ID );
		}
		else if( !glsr( Helper::class )->compareArrays( $reviewIds, $updatedReviewIds )) {
			$reviews = glsr( Database::class )->getReviews([
				'count' => -1,
				'post__in' => $updatedReviewIds,
			]);
			update_post_meta( $postId, static::META_AVERAGE, $this->recalculatePostAverage( $reviews->results ));
			update_post_meta( $postId, static::META_RANKING, $this->recalculatePostRanking( $reviews->results ));
		}
	}

	/**
	 * @param int $postId
	 * @return void
	 */
	protected function updateReviewIdOfPost( $postId, WP_Post $review, array $reviewIds )
	{
		if( $review->post_status != 'publish' ) {
			delete_post_meta( $postId, static::META_REVIEW_ID, $review->ID );
		}
		else if( !in_array( $review->ID, $reviewIds )) {
			add_post_meta( $postId, static::META_REVIEW_ID, $review->ID );
		}
	}
}
