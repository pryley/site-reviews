<?php

namespace GeminiLabs\SiteReviews\Controllers\EditorController;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Database\CountsManager;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Rating;
use WP_Post;

class Metaboxes
{
	const META_AVERAGE = '_glsr_average';
	const META_RANKING = '_glsr_ranking';
	const META_REVIEW_ID = '_glsr_review_id';

	/**
	 * @param int $postId
	 * @return void
	 */
	public function onBeforeDeleteReview( $postId )
	{
		if( get_post_field( 'post_type', $postId ) !== Application::POST_TYPE )return;
		$review = get_post( $postId );
		$review->post_status = 'deleted'; // change post_status first!
		$this->updateAssignedToPost( $review );
		$this->decreaseReviewCount( $review );
	}

	/**
	 * Update the review count when the rating or review_type changes
	 * @param string $metaKey
	 * @param mixed $metaValue
	 * @return void
	 */
	public function onBeforeUpdateReview( WP_Post $review, $metaKey, $newValue )
	{
		$previousValue = get_post_meta( $review->ID, $metaKey, true );
		if( $previousValue == $newValue )return;
		$this->decreaseReviewCount( $review );
		$this->increaseReviewCount( $review, [$metaKey => $newValue] );
	}

	/**
	 * @param array $postData
	 * @param array $meta
	 * @param int $postId
	 * @return void
	 */
	public function onCreateReview( $postData, $meta, $postId )
	{
		if( get_post_field( 'post_type', $postId ) !== Application::POST_TYPE )return;
		$review = get_post( $postId );
		$this->updateAssignedToPost( $review );
		$this->increaseReviewCount( $review );
	}

	/**
	 * Update the review count when the post_status changes
	 * @param string $status
	 * @return void
	 */
	public function onReviewStatusChange( $status, WP_Post $review )
	{
		if( $status == 'publish' ) {
			$this->increaseReviewCount( $review );
		}
		else {
			$this->decreaseReviewCount( $review );
		}
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
		if( !wp_verify_nonce( glsr( Helper::class )->filterInput( '_nonce-assigned-to' ), 'assigned_to' ))return;
		$assignedTo = glsr( Helper::class )->filterInput( 'assigned_to' );
		$assignedTo || $assignedTo = '';
		if( get_post_meta( $postId, 'assigned_to', true ) != $assignedTo ) {
			$this->onBeforeDeleteReview( $postId );
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
		$response = glsr( Helper::class )->filterInput( 'response' );
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
	 * @return array
	 */
	protected function getReviewCounts( WP_Post $review, array $meta = [] )
	{
		$meta = wp_parse_args( $meta, [
			'rating' => get_post_meta( $review->ID, 'rating', true ),
			'review_type' => get_post_meta( $review->ID, 'review_type', true ),
		]);
		return glsr( CountsManager::class )->get([
			'max' => $meta['rating'],
			'min' => 0,
			'types' => $meta['review_type'],
		]);
	}

	/**
	 * @return void
	 */
	protected function setReviewCounts( WP_Post $review, array $counts )
	{
		$type = strval( get_post_meta( $review->ID, 'review_type', true ));
		if( !array_key_exists( $type, glsr()->reviewTypes ))return;
		glsr( OptionManager::class )->set( 'counts.'.$type, $counts );
	}

	/**
	 * @return void
	 */
	protected function increaseReviewCount( WP_Post $review, array $meta = [] )
	{
		if( $counts = $this->getReviewCounts( $review, $meta )) {
			$rating = isset( $meta['rating'] )
				? $meta['rating']
				: intval( get_post_meta( $review->ID, 'rating', true ));
			$counts[$rating] -= 1;
			$this->setReviewCounts( $review, $counts );
		}
	}

	/**
	 * @return void
	 */
	protected function decreaseReviewCount( WP_Post $review, array $meta = [] )
	{
		if( $counts = $this->getReviewCounts( $review, $meta )) {
			$rating = intval( get_post_meta( $review->ID, 'rating', true ));
			$counts[$rating] += 1;
			$this->setReviewCounts( $review, $counts );
		}
	}

	/**
	 * @return int
	 */
	protected function recalculatePostAverage( array $ratingCounts )
	{
		return glsr( Rating::class )->getAverage( $ratingCounts );
	}

	/**
	 * @return int
	 */
	protected function recalculatePostRanking( array $ratingCounts )
	{
		return glsr( Rating::class )->getRanking( $ratingCounts );
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
			$counts = glsr( CountsManager::class )->buildFromIds( $updatedReviewIds );
			$counts = glsr( CountsManager::class )->flatten( $counts );
			update_post_meta( $postId, static::META_AVERAGE, $this->recalculatePostAverage( $counts ));
			update_post_meta( $postId, static::META_RANKING, $this->recalculatePostRanking( $counts ));
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
