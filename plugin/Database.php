<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Database\Cache;
use GeminiLabs\SiteReviews\Commands\CreateReview;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Database\QueryBuilder;
use GeminiLabs\SiteReviews\Database\SqlQueries;
use GeminiLabs\SiteReviews\Defaults\CreateReviewDefaults;
use GeminiLabs\SiteReviews\Defaults\GetReviewsDefaults;
use WP_Error;
use WP_Post;
use WP_Query;

class Database
{
	/**
	 * @return int|bool
	 */
	public function createReview( CreateReview $command )
	{
		$review = glsr( CreateReviewDefaults::class )->restrict( (array)$command );
		$review = apply_filters( 'site-reviews/create/review-values', $review, $command );
		glsr_log( $review )->debug( $command );
		$post = [
			'comment_status' => 'closed',
			'ping_status' => 'closed',
			'post_content' => $review['content'],
			'post_date' => $review['date'],
			'post_name' => $review['review_type'].'-'.$review['review_id'],
			'post_status' => $this->getNewPostStatus( $review, $command->blacklisted ),
			'post_title' => $review['title'],
			'post_type' => Application::POST_TYPE,
		];
		$postId = wp_insert_post( $post, true );
		if( is_wp_error( $postId )) {
			glsr_log()->error( $postId->get_error_message() );
			return false;
		}
		$this->setReviewMeta( $postId, $review, $command->category );
		do_action( 'site-reviews/create/review', $post, $review, $postId );
		return $postId;
	}

	/**
	 * Delete review based on a review_id meta value
	 * @param string $metaReviewId
	 * @return void
	 */
	public function deleteReview( $metaReviewId )
	{
		if( $postId = $this->getReviewPostId( $metaReviewId )) {
			wp_delete_post( $postId, true );
		}
	}

	/**
	 * @param int|WP_post $post
	 * @param string $assignedTo
	 * @return void|WP_Post
	 */
	public function getAssignedToPost( $post, $assignedTo = '' )
	{
		$post = get_post( $post );
		if( !( $post instanceof WP_Post ))return;
		if( empty( $assignedTo )) {
			$assignedTo = get_post_meta( $post->ID, 'assigned_to', true );
		}
		$assignedPost = get_post( $assignedTo );
		if( !empty( $assignedTo )
			&& $assignedPost instanceof WP_Post
			&& $assignedPost->ID != $post->ID ) {
			return $assignedPost;
		}
	}

	/**
	 * @param \WP_Post|null $post
	 * @return null|object
	 */
	public function getReview( $post )
	{
		if( !( $post instanceof WP_Post ) || $post->post_type != Application::POST_TYPE )return;
		$review = $this->getReviewMeta( $post->ID );
		$modified = $this->isReviewModified( $post, $review );
		$review->content = $post->post_content;
		$review->data = $post->post_date;
		$review->ID = $post->ID;
		$review->modifed = $modified;
		$review->status = $post->post_status;
		$review->title = $post->post_title;
		$review->user_id = $post->post_author;
		return apply_filters( 'site-reviews/get/review', $review, $post );
	}

	/**
	 * @param string $metaKey
	 * @param string $metaValue
	 * @return array|int
	 */
	public function getReviewCount( $metaKey = '', $metaValue = '' )
	{
		$metaKey = $this->normalizeMetaKey( $metaKey );
		if( !$metaKey ) {
			return (array) wp_count_posts( Application::POST_TYPE );
		}
		$counts = glsr( Cache::class )->getReviewCountsFor( $metaKey );
		if( !$metaValue ) {
			return $counts;
		}
		return isset( $counts[$metaValue] )
			? $counts[$metaValue]
			: 0;
	}

	/**
	 * @param string $metaReviewType
	 * @return array
	 */
	public function getReviewIdsByType( $metaReviewType )
	{
		return glsr( SqlQueries::class )->getReviewIdsByType( $metaReviewType );
	}

	/**
	 * @param int $postId
	 * @return object
	 */
	public function getReviewMeta( $postId )
	{
		$meta = get_post_type( $postId ) == Application::POST_TYPE
			? array_map( 'array_shift', (array) get_post_meta( $postId ))
			: [];
		return (object) $this->normalizeMeta( array_filter( $meta, 'strlen' ));
	}

	/**
	 * @param string $metaReviewId
	 * @return int
	 */
	public function getReviewPostId( $metaReviewId )
	{
		return glsr( SqlQueries::class )->getReviewPostId( $metaReviewId );
	}

	/**
	 * @return object
	 */
	public function getReviews( array $args = [] )
	{
		$args = glsr( GetReviewsDefaults::class )->restrict( $args );
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
			'posts_per_page' => $args['count'] ? $args['count'] : -1,
			'tax_query' => $taxQuery,
		]);
		return (object) [
			'results' => array_map( [$this, 'getReview'], $reviews->posts ),
			'max_num_pages' => $reviews->max_num_pages,
		];
	}

	/**
	 * @param string|array $keys
	 * @param string $status
	 * @return array
	 */
	public function getReviewsMeta( $keys, $status = 'publish' )
	{
		$keys = array_map( [$this, 'normalizeMetaKey'], (array)$keys );
		if( $status == 'all' || empty( $status )) {
			$status = get_post_stati( ['exclude_from_search' => false] );
		}
		return glsr( SqlQueries::class )->getReviewsMeta( $keys, $status );
	}

	/**
	 * @param string $taxonomy
	 * @return array
	 */
	public function getTerms( array $args = [] )
	{
		$args = wp_parse_args( $args, [
			'fields' => 'id=>name',
			'hide_empty' => false,
			'taxonomy' => Application::TAXONOMY,
		]);
		unset( $args['count'] ); //we don't want a term count
		$terms = get_terms( $args );
		if( is_wp_error( $terms )) {
			glsr_log()->error( $terms->get_error_message() );
			return [];
		}
		return $terms;
	}

	/**
	 * @return array
	 */
	public function normalizeMeta( array $meta )
	{
		if( empty( $meta )) {
			return [];
		}
		$defaults = wp_parse_args( $meta, [
			'author' => __( 'Anonymous', 'site-reviews' ),
			'date' => '',
			'review_id' => '',
			'review_type' => '',
		]);
		return glsr( CreateReviewDefaults::class )->restrict( $defaults );
	}

	/**
	 * @param string $metaKey
	 * @return string
	 */
	public function normalizeMetaKey( $metaKey )
	{
		$metaKey = strtolower( $metaKey );
		if( in_array( $metaKey, ['id', 'type'] )) {
			$metaKey = 'review_'.$metaKey;
		}
		return $metaKey;
	}

	/**
	 * @param string $termIds string of comma-separated term IDs
	 * @return array
	 */
	public function normalizeTerms( $termIds )
	{
		$terms = [];
		$termIds = array_map( 'trim', explode( ',', $termIds ));
		foreach( $termIds as $termId ) {
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
	public function revertReview( $postId )
	{
		$post = get_post( $postId );
		if( !( $post instanceof WP_Post ) || $post->post_type != Application::POST_TYPE )return;
		delete_post_meta( $post->ID, '_edit_last' );
		$result = wp_update_post([
			'ID' => $post->ID,
			'post_content' => get_post_meta( $post->ID, 'content', true ),
			'post_date' => get_post_meta( $post->ID, 'date', true ),
			'post_title' => get_post_meta( $post->ID, 'title', true ),
		]);
		if( is_wp_error( $result )) {
			glsr_log()->error( $result->get_error_message() );
		}
	}

	/**
	 * @param string $searchTerm
	 * @return void|string
	 */
	public function searchPosts( $searchTerm )
	{
		$args = [
			'post_status' => 'publish',
			'post_type' => 'any',
		];
		if( is_numeric( $searchTerm )) {
			$args['post__in'] = [$searchTerm];
		}
		else {
			$args['orderby'] = 'relevance';
			$args['posts_per_page'] = 10;
			$args['s'] = $searchTerm;
		}
		$queryBuilder = glsr( QueryBuilder::class );
		add_filter( 'posts_search', [$queryBuilder, 'filterSearchByTitle'], 500, 2 );
		$search = new WP_Query( $args );
		remove_filter( 'posts_search', [$queryBuilder, 'filterSearchByTitle'], 500 );
		if( !$search->have_posts() )return;
		$results = '';
		while( $search->have_posts() ) {
			$search->the_post();
			ob_start();
			glsr()->render( 'partials/editor/search-result', [
				'ID' => get_the_ID(),
				'permalink' => esc_url( (string) get_permalink() ),
				'title' => esc_attr( get_the_title() ),
			]);
			$results .= ob_get_clean();
		}
		wp_reset_postdata();
		return $results;
	}

	/**
	 * @param int $postId
	 * @param string $termIds
	 * @return void
	 */
	public function setReviewMeta( $postId, array $review, $termIds )
	{
		foreach( $review as $metaKey => $metaValue ) {
			update_post_meta( $postId, $metaKey, $metaValue );
		}
		$terms = $this->normalizeTerms( $termIds );
		if( empty( $terms ))return;
		$result = wp_set_object_terms( $postId, $terms, Application::TAXONOMY );
		if( is_wp_error( $result )) {
			glsr_log()->error( $result->get_error_message() );
		}
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
	 * @param object $review
	 * @return bool
	 */
	protected function isReviewModified( WP_Post $post, $review )
	{
		return $post->post_date != $review->date
			|| $post->post_content != $review->content
			|| $post->post_title != $review->title;
	}
}
