<?php

/**
 * Site Reviews Sumary shortcode
 *
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     2.3.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Shortcodes;

use GeminiLabs\SiteReviews\Rating;
use GeminiLabs\SiteReviews\Shortcode;

class SiteReviewsSummary extends Shortcode
{
	protected static $HIDDEN_KEYS = ['bars', 'rating', 'stars', 'summary'];

	/**
	 * @var array
	 */
	public $args;

	/**
	 * @var Rating
	 */
	public $rating;

	/**
	 * @return null|string
	 */
	public function printShortcode( $atts = [] )
	{
		$this->normalize( $atts );
		if( $this->isHidden() )return;
		$this->rating = $this->app->make( 'Rating' );
		$reviews = $this->db->getReviews( $this->args );
		$ratingAverage = $this->rating->getAverage( $reviews->reviews );
		if( !$this->canShowIfEmpty( $ratingAverage ))return;
		$this->buildSchema();
		ob_start();
		printf( '<div class="glsr-shortcode shortcode-site-reviews-summary %s">', $this->args['class'] );
		if( !empty( $this->args['title'] )) {
			printf( '<h3 class="glsr-shortcode-title">%s</h3>', $this->args['title'] );
		}
		echo $this->buildSummary( $ratingAverage, count( $reviews->reviews ));
		echo $this->buildPercentBars( $reviews->reviews );
		echo '</div>';
		return ob_get_clean();
	}

	/**
	 * @param string $label
	 * @param string $percentage
	 * @param string $count
	 * @return string
	 */
	protected function buildPercentBar( $label, $percentage, $count = null )
	{
		if( empty( $count )) {
			$count = $percentage;
		}
		return sprintf(
			'<div class="glsr-bar">' .
				'<span class="glsr-bar-label">%s</span>' .
				'<span class="glsr-bar-background"><span class="glsr-bar-percent" style="width:%s;"></span></span>' .
				'<span class="glsr-bar-count">%s</span>' .
			'</div>',
			$label,
			$percentage,
			$count
		);
	}

	/**
	 * @param int $maxRating
	 * @return null|string
	 */
	protected function buildPercentBars( array $reviews, $maxRating = 5 )
	{
		if( in_array( 'bars', $this->args['hide'] ))return;
		$bars = '';
		$ratingLabels = $this->args['labels'];
		$ratingPercentages = preg_filter( '/$/', '%', $this->rating->getPercentages( $reviews ));
		for( $i = $maxRating; $i > 0; $i-- ) {
			$bars .= $this->buildPercentBar( $ratingLabels[$i], $ratingPercentages[$i] );
		}
		return sprintf( '<div class="glsr-percentage-bars">%s</div>', $bars );
	}

	/**
	 * @param float $rating
	 * @return null|string
	 */
	protected function buildRating( $rating )
	{
		if( in_array( 'rating', $this->args['hide'] ))return;
		return sprintf( '<span class="glsr-summary-rating">%s</span>', $rating );
	}

	/**
	 * @return void
	 */
	protected function buildSchema()
	{
		if( !$this->args['schema'] )return;
		$schema = $this->app->make( 'Schema' );
		$schema->store( $schema->buildSummary( $this->args ));
	}

	/**
	 * @param float $rating
	 * @return null|string
	 */
	protected function buildStars( $rating )
	{
		if( in_array( 'stars', $this->args['hide'] ))return;
		return $this->app->make( 'Html' )->renderPartial( 'star-rating', [
			'rating' => $rating,
		]);
	}

	/**
	 * @param float $rating
	 * @param int $count
	 * @return null|string
	 */
	protected function buildSummary( $rating, $count )
	{
		if( $this->isHidden( ['rating', 'stars', 'summary'] ))return;
		return sprintf( '<div class="glsr-summary">%s</div>',
			$this->buildRating( $rating ) .
			$this->buildStars( $rating ) .
			$this->buildSummaryText( $rating, $count )
		);
	}

	/**
	 * @param float $rating
	 * @param int $count
	 * @return null|string
	 */
	protected function buildSummaryText( $rating, $count )
	{
		if( in_array( 'summary', $this->args['hide'] ))return;
		if( empty( $this->args['text'] )) {
			 $this->args['text'] = _nx(
				'{rating} out of {max} stars (based on %d review)',
				'{rating} out of {max} stars (based on %d reviews)',
				$count,
				'Do not translate {rating} and {max}, they are template tags.',
				'site-reviews'
			);
		}
		$summary = str_replace( ['{rating}','{max}'], [$rating, Rating::MAX_RATING], $this->args['text'] );
		$summary = str_replace( ['%s','%d'], $count, $summary );
		return sprintf( '<span class="glsr-summary-text">%s</span>', $summary );
	}

	/**
	 * @param int $averageRating
	 * @return bool
	 */
	protected function canShowIfEmpty( $averageRating )
	{
		return empty( $averageRating )
			? $this->args['show_if_empty']
			: true;
	}

	/**
	 * @return bool
	 */
	protected function isHidden( array $values = [] )
	{
		if( empty( $values )) {
			$values = static::$HIDDEN_KEYS;
		}
		return !array_diff( $values, $this->args['hide'] );
	}

	/**
	 * @return void
	 */
	protected function normalize( $atts )
	{
		$defaults = [
			'assigned_to' => '',
			'category' => '',
			'class' => '',
			'count' => -1,
			'hide' => '',
			'labels' => '',
			'rating' => 1,
			'schema' => false,
			'show_if_empty' => true,
			'text' => '',
			'title' => '',
			'type' => '',
		];
		$this->args = shortcode_atts( $defaults, $atts );
		array_walk( $this->args, function( &$value, $key ) {
			$methodName = $this->app->make( 'Helper' )->buildMethodName( $key, 'normalize' );
			if( !method_exists( $this, $methodName ))return;
			$value = $this->$methodName( $value );
		});
	}

	/**
	 * @param string $postId
	 * @return int|string
	 */
	protected function normalizeAssignedTo( $postId )
	{
		return $postId == 'post_id' ? intval( get_the_ID() ) : $postId;
	}

	/**
	 * @return array
	 */
	protected function normalizeHide( $hide )
	{
		$hidden = explode( ',', $hide );
		return array_filter( $hidden, function( $value ) {
			return in_array( $value, static::$HIDDEN_KEYS );
		});
	}

	/**
	 * @return array
	 */
	protected function normalizeLabels( $labels )
	{
		$defaults = [
			__( 'Excellent', 'site-reviews' ),
			__( 'Very good', 'site-reviews' ),
			__( 'Average', 'site-reviews' ),
			__( 'Poor', 'site-reviews' ),
			__( 'Terrible', 'site-reviews' ),
		];
		$labels = explode( ',', $labels );
		foreach( $defaults as $i => $label ) {
			if( empty( $labels[$i] ))continue;
			$defaults[$i] = $labels[$i];
		}
		return array_combine( [5,4,3,2,1], $defaults );
	}

	/**
	 * @return bool
	 */
	protected function normalizeSchema( $schema )
	{
		return wp_validate_boolean( $schema );
	}

	/**
	 * @return bool
	 */
	protected function normalizeShowIfEmpty( $showIfEmpty )
	{
		return wp_validate_boolean( $showIfEmpty );
	}

	/**
	 * @param string $text
	 * @return string
	 */
	protected function normalizeText( $text )
	{
		return trim( $text );
	}
}
