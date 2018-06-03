<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Partials;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Modules\Html;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Modules\Html\Partial;
use GeminiLabs\SiteReviews\Modules\Rating;
use GeminiLabs\SiteReviews\Modules\Schema;

class SiteReviewsSummary
{
	/**
	 * @var array
	 */
	protected $args;

	/**
	 * @var float
	 */
	protected $rating;

	/**
	 * @var object
	 */
	protected $reviews;

	/**
	 * @return void|string
	 */
	public function build( array $args = [] )
	{
		$this->args = $args;
		$this->reviews = glsr( Database::class )->getReviews( $args )->results;
		if( empty( $this->reviews ) && $this->isHidden( 'if_empty' ))return;
		$this->rating = glsr( Rating::class )->getAverage( $this->reviews );
		$this->generateSchema();
		return glsr( Template::class )->build( 'templates/reviews-summary', [
			'context' => [
				'class' => $this->getClass(),
				'id' => $this->args['id'],
				'percentages' => $this->buildPercentage(),
				'rating' => $this->buildRating(),
				'stars' => $this->buildStars(),
				'text' => $this->buildText(),
			],
		]);
	}

	/**
	 * @return void|string
	 */
	protected function buildPercentage()
	{
		if( $this->isHidden( 'bars' ))return;
		$range = range( Rating::MAX_RATING, 1 );
		$percentages = preg_filter( '/$/', '%', glsr( Rating::class )->getPercentages( $this->reviews ));
		$bars = array_reduce( $range, function( $carry, $level ) use( $percentages ) {
			$label = $this->buildPercentageLabel( $this->args['labels'][$level] );
			$background = $this->buildPercentageBackground( $percentages[$level] );
			$percent = $this->buildPercentagePercent( $percentages[$level] );
			return $carry.glsr( Builder::class )->div( $label.$background.$percent, [
				'class' => 'glsr-bar',
			]);
		});
		return $this->wrap( 'percentage', $bars );
	}

	/**
	 * @param string $percent
	 * @return string
	 */
	protected function buildPercentageBackground( $percent )
	{
		$backgroundPercent = glsr( Builder::class )->span([
			'class' => 'glsr-bar-background-percent',
			'style' => 'width:'.$percent,
		]);
		return '<span class="glsr-bar-background">'.$backgroundPercent.'</span>';
	}

	/**
	 * @param string $label
	 * @return string
	 */
	protected function buildPercentageLabel( $label )
	{
		return '<span class="glsr-bar-label">'.$label.'</span>';
	}

	/**
	 * @param string $percent
	 * @return string
	 */
	protected function buildPercentagePercent( $percent )
	{
		return '<span class="glsr-bar-percent">'.$percent.'</span>';
	}

	/**
	 * @return void|string
	 */
	protected function buildRating()
	{
		if( $this->isHidden( 'rating' ))return;
		return $this->wrap( 'rating', '<span>'.$this->rating.'</span>' );
	}

	/**
	 * @return void|string
	 */
	protected function buildStars()
	{
		if( $this->isHidden( 'stars' ))return;
		$stars = glsr( Partial::class )->build( 'star-rating', [
			'rating' => $this->rating,
		]);
		return $this->wrap( 'stars', $stars );
	}

	/**
	 * @return void|string
	 */
	protected function buildText()
	{
		if( $this->isHidden( 'summary' ))return;
		$count = count( $this->reviews );
		if( empty( $this->args['text'] )) {
			 $this->args['text'] = _nx(
				'{rating} out of {max} stars (based on %d review)',
				'{rating} out of {max} stars (based on %d reviews)',
				$count,
				'Do not translate {rating} and {max}, they are template tags.',
				'site-reviews'
			);
		}
		$summary = str_replace( ['{rating}','{max}'], [$this->rating, Rating::MAX_RATING], $this->args['text'] );
		$summary = str_replace( ['%s','%d'], $count, $summary );
		return $this->wrap( 'text', '<span>'.$summary.'</span>' );
	}

	/**
	 * @return void
	 */
	protected function generateSchema()
	{
		if( !wp_validate_boolean( $this->args['schema'] ))return;
		glsr( Schema::class )->store(
			glsr( Schema::class )->buildSummary( $this->args )
		);
	}

	/**
	 * @return string
	 */
	protected function getClass()
	{
		$style = apply_filters( 'site-reviews/style', 'glsr-style' );
		return trim( 'glsr-summary '.$style.' '.$this->args['class'] );
	}

	/**
	 * @param string $key
	 * @return bool
	 */
	protected function isHidden( $key )
	{
		return in_array( $key, $this->args['hide'] );
	}

	/**
	 * @param string $key
	 * @param string $value
	 * @return string
	 */
	protected function wrap( $key, $value )
	{
		return glsr( Builder::class )->div( $value, [
			'class' => 'glsr-summary-'.$key,
		]);
	}
}
