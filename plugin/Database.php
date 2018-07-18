<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Database\Cache;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Database\QueryBuilder;
use GeminiLabs\SiteReviews\Database\SqlQueries;
use GeminiLabs\SiteReviews\Modules\Rating;
use WP_Post;
use WP_Query;

class Database
{
	/**
	 * @return array
	 */
	public function buildReviewCounts()
	{
		$counts = [];
		$greaterThanId = 0;
		while( $reviews = glsr( SqlQueries::class )->getReviewRatings( $greaterThanId )) {
			$types = array_keys( array_flip( array_column( $reviews, 'type' )));
			foreach( $types as $type ) {
				if( isset( $counts[$type] ))continue;
				$counts[$type] = array_fill_keys( range( 0, Rating::MAX_RATING ), 0 );
			}
			foreach( $reviews as $review ) {
				$counts[$review->type][$review->rating]++;
			}
			$greaterThanId = end( $reviews )->ID;
		}
		return $counts;
	}

	/**
	 * @return array
	 */
	public function buildReviewCountsFromIds( array $postIds )
	{
		$counts = array_fill_keys( range( 0, Rating::MAX_RATING ), 0 );
		$greaterThanId = 0;
		while( $reviews = glsr( SqlQueries::class )->getReviewRatingsFromIds( $postIds, $greaterThanId )) {
			foreach( $reviews as $review ) {
				$counts[$review->rating]++;
			}
			$greaterThanId = end( $reviews )->ID;
		}
		return $counts;
	}

	/**
	 * @param int $postId
	 * @param string $assignedTo
	 * @return void|WP_Post
	 */
	public function getAssignedToPost( $postId, $assignedTo = '' )
	{
		if( empty( $assignedTo )) {
			$assignedTo = get_post_meta( $postId, 'assigned_to', true );
		}
		if( empty( $assignedTo ))return;
		$assignedPost = get_post( $assignedTo );
		if( $assignedPost instanceof WP_Post && $assignedPost->ID != $postId ) {
			return $assignedPost;
		}
	}

	/**
	 * @param string $metaKey
	 * @param string $metaValue
	 * @return array|int
	 */
	public function getReviewCount( $metaKey = '', $metaValue = '' )
	{
		if( !$metaKey ) {
			return (array)wp_count_posts( Application::POST_TYPE );
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
	 * @return array
	 */
	public function getReviewCounts( array $args = [] )
	{
		$args = wp_parse_args( $args, [
			'max' => Rating::MAX_RATING,
			'min' => Rating::MIN_RATING,
			'types' => 'local',
		]);
		$counts = array_intersect_key(
			glsr( OptionManager::class )->get( 'counts', [] ),
			array_flip( array_intersect( array_keys( glsr()->reviewTypes ), (array)$args['types'] ))
		);
		array_walk( $counts, function( &$ratings ) use( $args ) {
			$ratings[0] = 0;
			foreach( $ratings as $index => &$num ) {
				if( $index >= intval( $args['min'] ) && $index <= intval( $args['max'] ))continue;
				$num = 0;
			}
		});
		return $counts;
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
	 * @param string $key
	 * @param string $status
	 * @return array
	 */
	public function getReviewsMeta( $key, $status = 'publish' )
	{
		if( $status == 'all' || empty( $status )) {
			$status = get_post_stati( ['exclude_from_search' => false] );
		}
		return glsr( SqlQueries::class )->getReviewsMeta( $key, $status );
	}

	/**
	 * @return array
	 */
	public function getTerms( array $args = [] )
	{
		$args = wp_parse_args( $args, [
			'fields' => 'id=>name',
			'hide_empty' => false,
			'taxonomy' => Application::TAXONOMY,
		]);
		unset( $args['count'] ); // we don't want a term count
		$terms = get_terms( $args );
		if( is_wp_error( $terms )) {
			glsr_log()->error( $terms->get_error_message() );
			return [];
		}
		return $terms;
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
}
