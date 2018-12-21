<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Commands\CreateReview;
use GeminiLabs\SiteReviews\Database\DefaultsManager;
use GeminiLabs\SiteReviews\Database\QueryBuilder;
use GeminiLabs\SiteReviews\Database\SqlQueries;
use GeminiLabs\SiteReviews\Defaults\CreateReviewDefaults;
use GeminiLabs\SiteReviews\Defaults\ReviewsDefaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Review;
use WP_Post;
use WP_Query;

class ReviewManager
{
	/**
	 * @return false|Review
	 */
	public function create( CreateReview $command )
	{
		$reviewValues = glsr( CreateReviewDefaults::class )->restrict( (array)$command );
		$reviewValues = apply_filters( 'site-reviews/create/review-values', $reviewValues, $command );
		$postValues = [
			'comment_status' => 'closed',
			'meta_input' => $reviewValues,
			'ping_status' => 'closed',
			'post_content' => $reviewValues['content'],
			'post_date' => $reviewValues['date'],
			'post_name' => $reviewValues['review_type'].'-'.$reviewValues['review_id'],
			'post_status' => $this->getNewPostStatus( $reviewValues, $command->blacklisted ),
			'post_title' => $reviewValues['title'],
			'post_type' => Application::POST_TYPE,
		];
		$postId = wp_insert_post( $postValues, true );
		if( is_wp_error( $postId )) {
			glsr_log()->error( $postId->get_error_message() )->debug( $postValues );
			return false;
		}
		$this->setTerms( $postId, $command->category );
		$review = $this->single( get_post( $postId ));
		do_action( 'site-reviews/review/created', $review, $command );
		return $review;
	}

	/**
	 * @param string $metaReviewId
	 * @return void
	 */
	public function delete( $metaReviewId )
	{
		if( $postId = $this->getPostId( $metaReviewId )) {
			wp_delete_post( $postId, true );
		}
	}

	/**
	 * @return object
	 */
	public function get( array $args = [] )
	{
		$args = glsr( ReviewsDefaults::class )->restrict( $args );
		$metaQuery = glsr( QueryBuilder::class )->buildQuery(
			['assigned_to', 'type', 'rating'],
			$args
		);
		$taxQuery = glsr( QueryBuilder::class )->buildQuery(
			['category'],
			['category' => $this->normalizeTerms( $args['category'] )]
		);
		$paged = glsr( QueryBuilder::class )->getPaged(
			wp_validate_boolean( $args['pagination'] )
		);
		$reviews = new WP_Query([
			'meta_key' => 'pinned',
			'meta_query' => $metaQuery,
			'offset' => $args['offset'],
			'order' => $args['order'],
			'orderby' => 'meta_value '.$args['orderby'],
			'paged' => $paged,
			'post__in' => $args['post__in'],
			'post__not_in' => $args['post__not_in'],
			'post_status' => 'publish',
			'post_type' => Application::POST_TYPE,
			'posts_per_page' => $args['count'],
			'tax_query' => $taxQuery,
		]);
		return (object)[
			'results' => array_map( [$this, 'single'], $reviews->posts ),
			'max_num_pages' => $reviews->max_num_pages,
		];
	}

	/**
	 * @param string $metaReviewId
	 * @return int
	 */
	public function getPostId( $metaReviewId )
	{
		return glsr( SqlQueries::class )->getReviewPostId( $metaReviewId );
	}

	/**
	 * @param string $commaSeparatedTermIds
	 * @return array
	 */
	public function normalizeTerms( $commaSeparatedTermIds )
	{
		$terms = [];
		$termIds = glsr( Helper::class )->convertStringToArray( $commaSeparatedTermIds );
		foreach( $termIds as $termId ) {
			if( is_numeric( $termId )) {
				$termId = intval( $termId );
			}
			$term = term_exists( $termId, Application::TAXONOMY );
			if( !isset( $term['term_id'] ))continue;
			$terms[] = intval( $term['term_id'] );
		}
		return $terms;
	}

	/**
	 * @param int $postId
	 * @return void
	 */
	public function revert( $postId )
	{
		if( get_post_field( 'post_type', $postId ) != Application::POST_TYPE )return;
		delete_post_meta( $postId, '_edit_last' );
		$result = wp_update_post([
			'ID' => $postId,
			'post_content' => get_post_meta( $postId, 'content', true ),
			'post_date' => get_post_meta( $postId, 'date', true ),
			'post_title' => get_post_meta( $postId, 'title', true ),
		]);
		if( is_wp_error( $result )) {
			glsr_log()->error( $result->get_error_message() );
		}
	}

	/**
	 * @return Review
	 */
	public function single( WP_Post $post )
	{
		if( $post->post_type != Application::POST_TYPE )return;
		$review = new Review( $post );
		return apply_filters( 'site-reviews/get/review', $review, $post );
	}

	/**
	 * @param bool $isBlacklisted
	 * @return string
	 */
	protected function getNewPostStatus( array $review, $isBlacklisted )
	{
		$requireApprovalOption = glsr( OptionManager::class )->get( 'settings.general.require.approval' );
		return $review['review_type'] == 'local' && ( $requireApprovalOption == 'yes' || $isBlacklisted )
			? 'pending'
			: 'publish';
	}

	/**
	 * @param int $postId
	 * @param string $termIds
	 * @return void
	 */
	protected function setTerms( $postId, $termIds )
	{
		$terms = $this->normalizeTerms( $termIds );
		if( empty( $terms ))return;
		$result = wp_set_object_terms( $postId, $terms, Application::TAXONOMY );
		if( is_wp_error( $result )) {
			glsr_log()->error( $result->get_error_message() );
		}
	}
}
