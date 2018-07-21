<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Database\SqlQueries;
use GeminiLabs\SiteReviews\Modules\Rating;

class CountsManager
{
	const META_COUNT = '_glsr_count';

	/**
	 * @param int $limit
	 * @return array
	 */
	public function buildCounts( $limit = 500 )
	{
		return $this->build( $limit );
	}

	/**
	 * @param int $postId
	 * @param int $limit
	 * @return array
	 */
	public function buildPostCounts( $postId, $limit = 500 )
	{
		return $this->build( $limit, ['post_id' => $postId] );
	}

	/**
	 * @param int $termId
	 * @param int $limit
	 * @return array
	 */
	public function buildTermCounts( $termId, $limit = 500 )
	{
		return $this->build( $limit, ['term_id' => $termId] );
	}

	/**
	 * @param string $type
	 * @param int $rating
	 * @return array
	 */
	public function decrease( array $reviewCounts, $type, $rating )
	{
		if( isset( $reviewCounts[$type][$rating] )) {
			$reviewCounts[$type][$rating] = max( 0, $reviewCounts[$type][$rating] - 1 );
		}
		return $reviewCounts;
	}

	/**
	 * @return array
	 */
	public function flatten( array $reviewCounts )
	{
		$counts = [];
		array_walk_recursive( $reviewCounts, function( $num, $index ) use( &$counts ) {
			$counts[$index] = isset( $counts[$index] )
				? $num + $counts[$index]
				: $num;
		});
		return $counts;
	}

	/**
	 * @return array
	 */
	public function getCounts()
	{
		return glsr( OptionManager::class )->get( 'count', [] );
	}

	/**
	 * @param int $postId
	 * @return array
	 */
	public function getPostCounts( $postId )
	{
		return (array)get_post_meta( $postId, static::META_COUNT, true );
	}

	/**
	 * @param int $termId
	 * @return array
	 */
	public function getTermCounts( $termId )
	{
		return (array)get_term_meta( $termId, static::META_COUNT, true );
	}

	/**
	 * @param string $type
	 * @param int $rating
	 * @return array
	 */
	public function increase( array $reviewCounts, $type, $rating )
	{
		if( !array_key_exists( $type, glsr()->reviewTypes )) {
			return $reviewCounts;
		}
		if( !array_key_exists( $type, $reviewCounts )) {
			$reviewCounts[$type] = [];
		}
		$reviewCounts = $this->normalize( $reviewCounts );
		$reviewCounts[$type][$rating] = intval( $reviewCounts[$type][$rating] ) + 1;
		return $reviewCounts;
	}

	/**
	 * @return void
	 */
	public function setCounts( array $reviewCounts )
	{
		glsr( OptionManager::class )->set( 'count', $reviewCounts );
	}

	/**
	 * @param int $postId
	 * @return void
	 */
	public function setPostCounts( $postId, array $reviewCounts )
	{
		update_post_meta( $postId, static::META_COUNT, $reviewCounts );
	}

	/**
	 * @param int $termId
	 * @return void
	 */
	public function setTermCounts( $termId, array $reviewCounts )
	{
		update_term_meta( $termId, static::META_COUNT, $reviewCounts );
	}

	/**
	 * @param int $limit
	 * @return array
	 */
	protected function build( $limit, array $args = [] )
	{
		$counts = [];
		$lastPostId = 0;
		while( $reviews = $this->queryReviews( $args, $lastPostId, $limit )) {
			$types = array_keys( array_flip( array_column( $reviews, 'type' )));
			foreach( $types as $type ) {
				if( isset( $counts[$type] ))continue;
				$counts[$type] = array_fill_keys( range( 0, Rating::MAX_RATING ), 0 );
			}
			foreach( $reviews as $review ) {
				$counts[$review->type][$review->rating]++;
			}
			$lastPostId = end( $reviews )->ID;
		}
		return $counts;
	}

	/**
	 * @return array
	 */
	protected function normalize( array $reviewCounts )
	{
		foreach( $reviewCounts as &$counts ) {
			foreach( range( 0, Rating::MAX_RATING ) as $index ) {
				if( isset( $counts[$index] ))continue;
				$counts[$index] = 0;
			}
			ksort( $counts );
		}
		return $reviewCounts;
	}

	/**
	 * @param int $lastPostId
	 * @param int $limit
	 * @return void|array
	 */
	protected function queryReviews( array $args = [], $lastPostId, $limit )
	{
		$args = wp_parse_args( $args, array_fill_keys( ['post_id', 'term_id'], '' ));
		if( empty( array_filter( $args ))) {
			return glsr( SqlQueries::class )->getReviewCounts( $lastPostId, $limit );
		}
		if( !empty( $args['post_id'] )) {
			return glsr( SqlQueries::class )->getReviewPostCounts( $args['post_id'], $lastPostId, $limit );
		}
		if( !empty( $args['term_id'] )) {
			return glsr( SqlQueries::class )->getReviewTermCounts( $args['term_id'], $lastPostId, $limit );
		}
	}
}
