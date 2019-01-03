<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Database\SqlQueries;
use GeminiLabs\SiteReviews\Modules\Polylang;
use GeminiLabs\SiteReviews\Modules\Rating;
use GeminiLabs\SiteReviews\Review;
use WP_Post;
use WP_Term;

class CountsManager
{
	const META_AVERAGE = '_glsr_average';
	const META_COUNT = '_glsr_count';
	const META_RANKING = '_glsr_ranking';

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
	 * @param int $termTaxonomyId
	 * @param int $limit
	 * @return array
	 */
	public function buildTermCounts( $termTaxonomyId, $limit = 500 )
	{
		return $this->build( $limit, ['term_taxonomy_id' => $termTaxonomyId] );
	}

	/**
	 * @return void
	 */
	public function decrease( Review $review )
	{
		$this->decreaseCounts( $review );
		$this->decreasePostCounts( $review );
		$this->decreaseTermCounts( $review );
	}

	/**
	 * @return void
	 */
	public function decreaseCounts( Review $review )
	{
		$this->setCounts( $this->decreaseRating(
			$this->getCounts(),
			$review->review_type,
			$review->rating
		));
	}

	/**
	 * @return void
	 */
	public function decreasePostCounts( Review $review )
	{
		if( empty( $counts = $this->getPostCounts( $review->assigned_to )))return;
		$counts = $this->decreaseRating( $counts, $review->review_type, $review->rating );
		$this->setPostCounts( $review->assigned_to, $counts );
	}

	/**
	 * @return void
	 */
	public function decreaseTermCounts( Review $review )
	{
		foreach( $review->term_ids as $termId ) {
			if( empty( $counts = $this->getTermCounts( $termId )))continue;
			$counts = $this->decreaseRating( $counts, $review->review_type, $review->rating );
			$this->setTermCounts( $termId, $counts );
		}
	}

	/**
	 * @return array
	 */
	public function flatten( array $reviewCounts, array $args = [] )
	{
		$counts = [];
		array_walk_recursive( $reviewCounts, function( $num, $index ) use( &$counts ) {
			$counts[$index] = isset( $counts[$index] )
				? $num + $counts[$index]
				: $num;
		});
		$args = wp_parse_args( $args, [
			'max' => Rating::MAX_RATING,
			'min' => Rating::MIN_RATING,
		]);
		foreach( $counts as $index => &$num ) {
			if( $index >= intval( $args['min'] ) && $index <= intval( $args['max'] ))continue;
			$num = 0;
		}
		return $counts;
	}

	/**
	 * @return array
	 */
	public function get( array $args = [] )
	{
		$args = wp_parse_args( $args, [
			'post_ids' => [],
			'term_ids' => [],
			'type' => 'local',
		]);
		$counts = [];
		foreach( glsr( Polylang::class )->getPostIds( $args['post_ids'] ) as $postId ) {
			$counts[] = $this->getPostCounts( $postId );
		}
		foreach( $args['term_ids'] as $termId ) {
			$counts[] = $this->getTermCounts( $termId );
		}
		if( empty( $counts )) {
			$counts[] = $this->getCounts();
		}
		return in_array( $args['type'], ['', 'all'] )
			? $this->normalize( [$this->flatten( $counts )] )
			: $this->normalize( glsr_array_column( $counts, $args['type'] ));
	}

	/**
	 * @return array
	 */
	public function getCounts()
	{
		$counts = glsr( OptionManager::class )->get( 'counts', [] );
		if( !is_array( $counts )) {
			glsr_log()->error( 'CountsManager: counts is not an array' )->debug( $counts );
			return [];
		}
		return $counts;
	}

	/**
	 * @param int $postId
	 * @return array
	 */
	public function getPostCounts( $postId )
	{
		return array_filter( (array)get_post_meta( $postId, static::META_COUNT, true ));
	}

	/**
	 * @param int $termId
	 * @return array
	 */
	public function getTermCounts( $termId )
	{
		return array_filter( (array)get_term_meta( $termId, static::META_COUNT, true ));
	}

	/**
	 * @return void
	 */
	public function increase( Review $review )
	{
		$this->increaseCounts( $review );
		$this->increasePostCounts( $review );
		$this->increaseTermCounts( $review );
	}

	/**
	 * @return void
	 */
	public function increaseCounts( Review $review )
	{
		if( empty( $counts = $this->getCounts() )) {
			$counts = $this->buildCounts();
		}
		$this->setCounts( $this->increaseRating(
			$counts,
			$review->review_type,
			$review->rating
		));
	}

	/**
	 * @return void
	 */
	public function increasePostCounts( Review $review )
	{
		if( !( get_post( $review->assigned_to ) instanceof WP_Post ))return;
		$counts = $this->getPostCounts( $review->assigned_to );
		$counts = empty( $counts )
			? $this->buildPostCounts( $review->assigned_to )
			: $this->increaseRating( $counts, $review->review_type, $review->rating );
		$this->setPostCounts( $review->assigned_to, $counts );
	}

	/**
	 * @return void
	 */
	public function increaseTermCounts( Review $review )
	{
		$terms = glsr( ReviewManager::class )->normalizeTerms( implode( ',', $review->term_ids ));
		foreach( $terms as $term ) {
			$counts = $this->getTermCounts( $term['term_id'] );
			$counts = empty( $counts )
				? $this->buildTermCounts( $term['term_taxonomy_id'] )
				: $this->increaseRating( $counts, $review->review_type, $review->rating );
			$this->setTermCounts( $term['term_id'], $counts );
		}
	}

	/**
	 * @return void
	 */
	public function setCounts( array $reviewCounts )
	{
		glsr( OptionManager::class )->set( 'counts', $reviewCounts );
	}

	/**
	 * @param int $postId
	 * @return void
	 */
	public function setPostCounts( $postId, array $reviewCounts )
	{
		$ratingCounts = $this->flatten( $reviewCounts );
		update_post_meta( $postId, static::META_COUNT, $reviewCounts );
		update_post_meta( $postId, static::META_AVERAGE, glsr( Rating::class )->getAverage( $ratingCounts ));
		update_post_meta( $postId, static::META_RANKING, glsr( Rating::class )->getRanking( $ratingCounts ));
	}

	/**
	 * @param int $termId
	 * @return void
	 */
	public function setTermCounts( $termId, array $reviewCounts )
	{
		$term = get_term( $termId, Application::TAXONOMY );
		if( !isset( $term->term_id ))return;
		$ratingCounts = $this->flatten( $reviewCounts );
		update_term_meta( $termId, static::META_COUNT, $reviewCounts );
		update_term_meta( $termId, static::META_AVERAGE, glsr( Rating::class )->getAverage( $ratingCounts ));
		update_term_meta( $termId, static::META_RANKING, glsr( Rating::class )->getRanking( $ratingCounts ));
	}

	/**
	 * @param int $limit
	 * @return array
	 * @todo verify the additional type checks are needed
	 */
	protected function build( $limit, array $args = [] )
	{
		$counts = [];
		$lastPostId = 0;
		while( $reviews = $this->queryReviews( $args, $lastPostId, $limit )) {
			$types = array_keys( array_flip( glsr_array_column( $reviews, 'type' )));
			$types = array_unique( array_merge( ['local'], $types ));
			foreach( $types as $type ) {
				$type = $this->normalizeType( $type );
				if( isset( $counts[$type] ))continue;
				$counts[$type] = array_fill_keys( range( 0, Rating::MAX_RATING ), 0 );
			}
			foreach( $reviews as $review ) {
				$type = $this->normalizeType( $review->type );
				$counts[$type][$review->rating]++;
			}
			$lastPostId = end( $reviews )->ID;
		}
		return $counts;
	}

	/**
	 * @param string $type
	 * @param int $rating
	 * @return array
	 */
	protected function decreaseRating( array $reviewCounts, $type, $rating )
	{
		if( isset( $reviewCounts[$type][$rating] )) {
			$reviewCounts[$type][$rating] = max( 0, $reviewCounts[$type][$rating] - 1 );
		}
		return $reviewCounts;
	}

	/**
	 * @param string $type
	 * @param int $rating
	 * @return array
	 */
	protected function increaseRating( array $reviewCounts, $type, $rating )
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
	 * @return array
	 */
	protected function normalize( array $reviewCounts )
	{
		if( empty( $reviewCounts )) {
			$reviewCounts = [[]];
		}
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
	 * @param string $type
	 * @return string
	 */
	protected function normalizeType( $type )
	{
		return empty( $type ) || !is_string( $type )
			? 'local'
			: $type;
	}

	/**
	 * @param int $lastPostId
	 * @param int $limit
	 * @return void|array
	 */
	protected function queryReviews( array $args = [], $lastPostId, $limit )
	{
		$args = wp_parse_args( $args, array_fill_keys( ['post_id', 'term_taxonomy_id'], '' ));
		if( empty( array_filter( $args ))) {
			return glsr( SqlQueries::class )->getReviewCounts( $lastPostId, $limit );
		}
		if( !empty( $args['post_id'] )) {
			return glsr( SqlQueries::class )->getReviewPostCounts( $args['post_id'], $lastPostId, $limit );
		}
		if( !empty( $args['term_taxonomy_id'] )) {
			return glsr( SqlQueries::class )->getReviewTermCounts( $args['term_taxonomy_id'], $lastPostId, $limit );
		}
	}
}
