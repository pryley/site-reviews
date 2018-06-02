<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Partials;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Modules\Html;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
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
		$this->reviews = glsr( Database::class )->getReviews( $args );
		if( $this->isHidden() )return;
		$this->rating = glsr( Rating::class )->getAverage( $this->reviews->results );
		$this->buildSchema();
		return glsr( Builder::class )->div( $this->buildSummary().$this->buildPercentageBars(), [
			'class' => 'glsr-summary-wrap '.$args['class'],
			'id' => $args['id'],
		]);
	}

	/**
	 * @param int $index
	 * @param string $percent
	 * @return string
	 */
	protected function buildPercentageBar( $index, $percent )
	{
		$build = glsr( Builder::class );
		$label = $build->span( $this->args['labels'][$index], [
			'class' => 'glsr-bar-label',
		]);
		$barBackground = $build->span([
			'class' => 'glsr-bar-percent',
			'style' => 'width:'.$percent,
		]);
		$barPercent = $build->span( $barBackground, [
			'class' => 'glsr-bar-background',
		]);
		$percent = $build->span( $percent, [
			'class' => 'glsr-bar-count',
		]);
		return $build->div( $label.$barPercent.$percent, [
			'class' => 'glsr-percentage-bar',
		]);
	}

	/**
	 * @return void|string
	 */
	protected function buildPercentageBars()
	{
		if( in_array( 'bars', $this->args['hide'] ))return;
		$percentages = preg_filter( '/$/', '%', glsr( Rating::class )->getPercentages( $this->reviews->results ));
		$range = range( Rating::MAX_RATING, 1 );
		$bars = array_reduce( $range, function( $carry, $index ) use( $percentages ) {
			return $carry.$this->buildPercentageBar( intval( $index ), $percentages[$index] );
		});
		return glsr( Builder::class )->div( $bars, [
			'class' => 'glsr-percentage-bars',
		]);
	}

	/**
	 * @return void
	 */
	protected function buildSchema()
	{
		if( !$this->args['schema'] )return;
		$schema = glsr( Schema::class );
		$schema->store( $schema->buildSummary( $this->args ));
	}

	/**
	 * @return void|string
	 */
	protected function buildSummary()
	{
		$summary = $this->buildSummaryRating().$this->buildSummaryStars().$this->buildSummaryText();
		if( empty( $summary ))return;
		return glsr( Builder::class )->div( $summary, [
			'class' => 'glsr-summary',
		]);
	}

	/**
	 * @return void|string
	 */
	protected function buildSummaryRating()
	{
		if( in_array( 'rating', $this->args['hide'] ))return;
		return glsr( Builder::class )->span( $this->rating, [
			'class' => 'glsr-summary-rating',
		]);
	}

	/**
	 * @return void|string
	 */
	protected function buildSummaryStars()
	{
		if( in_array( 'stars', $this->args['hide'] ))return;
		$stars = glsr( Html::class )->buildPartial( 'star-rating', [
			'rating' => $this->rating,
		]);
		return glsr( Builder::class )->span( $stars, [
			'class' => 'glsr-summary-stars',
		]);
	}

	/**
	 * @return void|string
	 */
	protected function buildSummaryText()
	{
		if( in_array( 'summary', $this->args['hide'] ))return;
		$count = count( $this->reviews->results );
		if( empty( $this->args['text'] )) {
			 $this->args['text'] = _nx(
				'{rating} out of {max} stars (based on %d review)',
				'{rating} out of {max} stars (based on %d reviews)',
				$count,
				'Do not translate {rating} and {max}, they are template tags.',
				'site-reviews'
			);
		}
		$summary = str_replace(
			['{rating}','{max}'], [$this->rating, Rating::MAX_RATING], $this->args['text']
		);
		return str_replace( ['%s','%d'], $count, $summary );
	}

	/**
	 * @return bool
	 */
	protected function isHidden()
	{
		return empty( $this->reviews->results ) && in_array( 'if_empty', $this->args['hide'] );
	}
}
