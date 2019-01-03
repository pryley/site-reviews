<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Database;
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
	 * @param array $termTaxonomyIds
	 * @param string $taxonomySlug
	 * @param bool $append
	 * @param array $oldTermTaxonomyIds
	 * @return void
	 * @action set_object_terms
	 */
	public function onAfterChangeCategory( $postId, $terms, $termTaxonomyIds, $taxonomySlug, $append, $oldTermTaxonomyIds )
	{
		sort( $termTaxonomyIds );
		sort( $oldTermTaxonomyIds );
		if( $termTaxonomyIds === $oldTermTaxonomyIds || !$this->isReviewPostId( $postId ))return;
		$review = glsr( ReviewManager::class )->single( get_post( $postId ));
		$ignoredIds = array_intersect( $oldTermTaxonomyIds, $termTaxonomyIds );
		$decreasedIds = array_diff( $oldTermTaxonomyIds, $ignoredIds );
		$increasedIds = array_diff( $termTaxonomyIds, $ignoredIds );
		if( $review->term_ids = glsr( Database::class )->getTermIds( $decreasedIds, 'term_taxonomy_id' )) {
			glsr( CountsManager::class )->decreaseTermCounts( $review );
		}
		if( $review->term_ids = glsr( Database::class )->getTermIds( $increasedIds, 'term_taxonomy_id' )) {
			glsr( CountsManager::class )->increaseTermCounts( $review );
		}
	}

	/**
	 * @param string $oldStatus
	 * @param string $newStatus
	 * @return void
	 * @action transition_post_status
	 */
	public function onAfterChangeStatus( $newStatus, $oldStatus, WP_Post $post )
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

	/**
	 * @return void
	 * @action site-reviews/review/created
	 */
	public function onAfterCreate( Review $review )
	{
		if( $review->status !== 'publish' )return;
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
}
