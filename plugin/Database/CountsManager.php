<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Database\SqlQueries;
use GeminiLabs\SiteReviews\Modules\Rating;

class CountsManager
{
	/**
	 * @return array
	 */
	public function build( array $args = [], $limit = 500 )
	{
		$counts = [];
		$greaterThanId = 0;
		while( $reviews = glsr( SqlQueries::class )->getReviewRatings( $args, $greaterThanId, $limit )) {
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
	public function buildFromIds( array $postIds, $limit = 100 )
	{
		$counts = array_fill_keys( range( 0, Rating::MAX_RATING ), 0 );
		$greaterThanId = 0;
		while( $reviews = glsr( SqlQueries::class )->getReviewRatingsFromIds( $postIds, $greaterThanId, $limit )) {
			foreach( $reviews as $review ) {
				$counts[$review->rating]++;
			}
			$greaterThanId = end( $reviews )->ID;
		}
		return $counts;
	}

	/**
	 * @return array
	 */
	public function flatten( array $reviewCounts )
	{
		$counts = [];
		array_walk_recursive( $reviewCounts, function( $num, $index ) use( &$counts ) {
			$counts[$index] = isset($counts[$index])
				? $num + $counts[$index]
				: $num;
		});
		return $counts;
	}

	/**
	 * @return array
	 */
	public function get( array $args = [] )
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
		$counts = $this->normalize( $counts );
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
	 * @return array
	 */
	public function getFlattened( array $args = [] )
	{
		return $this->flatten( $this->get( $args ));
	}

	/**
	 * @return array
	 */
	protected function normalize( array $reviewCounts )
	{
		foreach( $reviewCounts as $type => &$counts ) {
			foreach( range( 0, Rating::MAX_RATING ) as $rating ) {
				if( isset( $counts[$rating] ))continue;
				$counts[$rating] = 0;
			}
			ksort( $counts );
		}
		return $reviewCounts;
	}
}

