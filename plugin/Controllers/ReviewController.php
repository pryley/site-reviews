<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Database\CountsManager;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Review;
use WP_Post;

class ReviewController extends Controller
{
	/**
	 * @param int $postId
	 * @param array $terms
	 * @param array $termIds
	 * @param string $taxonomySlug
	 * @param bool $append
	 * @param array $oldTermIds
	 * @return void
	 * @action set_object_terms
	 */
	public function onAfterChangeCategory( $postId, $terms, $termIds, $taxonomySlug, $append, $oldTermIds )
	{
		sort( $termIds );
		sort( $oldTermIds );
		if( $termIds === $oldTermIds || !$this->isReviewPostId( $postId ))return;
		$review = glsr( ReviewManager::class )->single( get_post( $postId ));
		$ignoredTerms = array_intersect( $oldTermIds, $termIds );
		if( $review->term_ids = array_diff( $oldTermIds, $ignoredTerms )) {
			glsr( CountsManager::class )->decreaseTermCounts( $review );
		}
		if( $review->term_ids = array_diff( $termIds, $ignoredTerms )) {
			glsr( CountsManager::class )->increaseTermCounts( $review );
		}
	}

	/**
	 * @return void
	 * @action site-reviews/review/created
	 */
	public function onAfterCreate( Review $review )
	{
		glsr( CountsManager::class )->increase( $review );
	}

	/**
	 * @param int $postId
	 * @return void
	 * @action before_delete_post
	 */
	public function onBeforeDelete( $postId )
	{
		if( !$this->isReviewPostId( $postId ))return;
		$review = glsr( ReviewManager::class )->single( get_post( $postId ));
		glsr( CountsManager::class )->decrease( $review );
	}

	/**
	 * @param int $metaId
	 * @param int $postId
	 * @param string $metaKey
	 * @param mixed $metaValue
	 * @return void
	 * @action update_postmeta
	 */
	public function onBeforeUpdate( $metaId, $postId, $metaKey, $metaValue )
	{
		if( !$this->isReviewPostId( $postId )
			|| !in_array( $metaKey, ['assigned_to', 'rating', 'review_type'] )
		)return;
		$review = glsr( ReviewManager::class )->single( get_post( $postId ));
		if( $review->$metaKey == $metaValue )return;
		$method = glsr( Helper::class )->buildMethodName( $metaKey, 'onBeforeChange' );
		call_user_func( [$this, $method], $review, $metaValue );
	}

	/**
	 * @param string|int $assignedTo
	 * @return void
	 */
	public function onBeforeChangeAssignedTo( Review $review, $assignedTo )
	{
		glsr( CountsManager::class )->decreasePostCounts( $review );
		$review->assigned_to = $assignedTo;
		glsr( CountsManager::class )->increasePostCounts( $review );
	}

	/**
	 * @param string|int $rating
	 * @return void
	 */
	public function onBeforeChangeRating( Review $review, $rating )
	{
		glsr( CountsManager::class )->decrease( $review );
		$review->rating = $rating;
		glsr( CountsManager::class )->increase( $review );
	}

	/**
	 * @param string $reviewType
	 * @return void
	 */
	public function onBeforeChangeReviewType( Review $review, $reviewType )
	{
		glsr( CountsManager::class )->decrease( $review );
		$review->review_type = $reviewType;
		glsr( CountsManager::class )->increase( $review );
	}

	/**
	 * @param string $oldStatus
	 * @param string $newStatus
	 * @return void
	 * @action transition_post_status
	 */
	public function onChangeStatus( $newStatus, $oldStatus, WP_Post $post )
	{
		if( $post->post_type != Application::POST_TYPE || in_array( $oldStatus, ['new', $newStatus] ))return;
		$review = glsr( ReviewManager::class )->single( get_post( $post->ID ));
		if( $post->post_status == 'publish' ) {
			glsr( CountsManager::class )->increase( $review );
		}
		else {
			glsr( CountsManager::class )->decrease( $review );
		}
	}
}
