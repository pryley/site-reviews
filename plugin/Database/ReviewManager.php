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
use GeminiLabs\SiteReviews\Reviews;
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
			'post_date_gmt' => get_gmt_from_date( $reviewValues['date'] ),
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
			['category' => $this->normalizeTermIds( $args['category'] )]
		);
		$paged = glsr( QueryBuilder::class )->getPaged(
			wp_validate_boolean( $args['pagination'] )
		);
		$query = new WP_Query([
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
		$results = array_map( [$this, 'single'], $query->posts );
		$reviews = new Reviews( $results, $query->max_num_pages, $args );
		return apply_filters( 'site-reviews/get/reviews', $reviews, $query );
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
	public function normalizeTermIds( $commaSeparatedTermIds )
	{
		$termIds = glsr_array_column( $this->normalizeTerms( $commaSeparatedTermIds ), 'term_id' );
		return array_unique( array_map( 'intval', $termIds ));
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
			$terms[] = $term;
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
		if( $post->post_type != Application::POST_TYPE ) {
			$post = new WP_Post( (object)[] );
		}
		$review = new Review( $post );
		return apply_filters( 'site-reviews/get/review', $review, $post );
	}

	/**
	 * @param bool $isBlacklisted
	 * @return string
	 */
	protected function getNewPostStatus( array $review, $isBlacklisted )
	{
		$requireApproval = glsr( OptionManager::class )->getBool( 'settings.general.require.approval' );
		return $review['review_type'] == 'local' && ( $requireApproval || $isBlacklisted )
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
		$termIds = $this->normalizeTermIds( $termIds );
		if( empty( $termIds ))return;
		$termTaxonomyIds = wp_set_object_terms( $postId, $termIds, Application::TAXONOMY );
		if( is_wp_error( $termTaxonomyIds )) {
			glsr_log()->error( $termTaxonomyIds->get_error_message() );
		}
	}
}
