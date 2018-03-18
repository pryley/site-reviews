<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2017, Paul Ryley
 * @license   GPLv3
 * @since     2.3.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews;

class Rating
{
	/**
	 * The more sure we are of the confidence interval (the higher the confidence level), the less
	 * precise the estimation will be as the margin for error will be higher.
	 * @see http://homepages.math.uic.edu/~bpower6/stat101/Confidence%20Intervals.pdf
	 * @see https://www.thecalculator.co/math/Confidence-Interval-Calculator-210.html
	 * @see https://www.youtube.com/watch?v=grodoLzThy4
	 * @see https://en.wikipedia.org/wiki/Standard_score
	 * @var array
	 */
	protected static $CONFIDENCE_LEVEL_Z_SCORES = [
		50     => 0.67449,
		70     => 1.04,
		75     => 1.15035,
		80     => 1.282,
		85     => 1.44,
		90     => 1.64485,
		92     => 1.75,
		95     => 1.95996,
		96     => 2.05,
		97     => 2.17009,
		98     => 2.326,
		99     => 2.57583,
		'99.5' => 2.81,
		'99.8' => 3.08,
		'99.9' => 3.29053,
	];

	/**
	 * @var int
	 */
	const MAX_RATING = 5;

	/**
	 * @var int
	 */
	const MIN_RATING = 1;

	/**
	 * Get the average rating for an array of reviews
	 * @param int $roundBy
	 * @return int|float
	 */
	public function getAverage( array $reviews, $roundBy = 1 )
	{
		return ( $ratingCount = count( $reviews ))
			? round( $this->getTotal( $reviews ) / $ratingCount, intval( $roundBy ))
			: 0;
	}

	/**
	 * @return array
	 */
	public function getCounts( array $reviews )
	{
		$counts = array_fill_keys( [5,4,3,2,1], [] );
		array_walk( $counts, function( &$count, $key ) use( $reviews ) {
			$count = count( array_filter( $reviews, function( $review ) use( $key ) {
				if( !isset( $review->rating ))return;
				return $review->rating == $key;
			}));
		});
		return $counts;
	}

	/**
	 * Get the lower bound for up/down ratings
	 * Method receives an up/down ratings array: [1, -1, -1, 1, 1, -1]
	 * @see http://www.evanmiller.org/how-not-to-sort-by-average-rating.html
	 * @see https://news.ycombinator.com/item?id=10481507
	 * @see https://dataorigami.net/blogs/napkin-folding/79030467-an-algorithm-to-sort-top-comments
	 * @see http://julesjacobs.github.io/2015/08/17/bayesian-scoring-of-ratings.html
	 * @param int $confidencePercentage
	 * @return int|float
	 */
	public function getLowerBound( array $upDownRatings, $confidencePercentage = 95 )
	{
		$numRatings = count( $upDownRatings );
		if( !$numRatings )return 0;
		$positiveRatings = count( array_filter( $upDownRatings, function( $value ) {
			return $value > 0;
		}));
		$z = static::$CONFIDENCE_LEVEL_Z_SCORES[$confidencePercentage];
		$phat = 1 * $positiveRatings / $numRatings;
		return ( $phat + $z * $z / ( 2 * $numRatings ) - $z * sqrt(( $phat * ( 1 - $phat ) + $z * $z / ( 4 * $numRatings )) / $numRatings )) / ( 1 + $z * $z / $numRatings );
	}

	/**
	 * Get the overall percentage rating for an array of reviews
	 * @return int|float
	 */
	public function getPercentage( array $reviews )
	{
		return round( $this->getAverage( $reviews ) * 100 / static::MAX_RATING, 2 );
	}

	/**
	 * Get the percentage ratings for an array of reviews
	 * @return array
	 */
	public function getPercentages( array $reviews )
	{
		$counts = $this->getCounts( $reviews );
		array_walk( $counts, function( &$count, $rating ) use( $counts ) {
			$total = array_sum( $counts );
			$count = !empty( $total ) && !empty( $counts[$rating] )
				? $counts[$rating] / array_sum( $counts ) * 100
				: 0;
		});
		return $this->getRoundedPercentages( $counts );
	}

	/**
	 * The quality of a 5 star rating depends not only on the average number of stars but also on
	 * the number of reviews. This method calculates the bayesian ranking of a page by its number
	 * of reviews and their rating.
	 * @see http://www.evanmiller.org/ranking-items-with-star-ratings.html
	 * @see https://stackoverflow.com/questions/1411199/what-is-a-better-way-to-sort-by-a-5-star-rating/1411268
	 * @see http://julesjacobs.github.io/2015/08/17/bayesian-scoring-of-ratings.html
	 * @param int $confidencePercentage
	 * @return float
	 */
	public function getRanking( array $reviews, $confidencePercentage = 90 )
	{
		$ratingCounts = $this->getCounts( $reviews );
		$ratingCountsSum = array_sum( $ratingCounts ) + count( $ratingCounts );
		$weight = $this->getWeight( $ratingCounts, $ratingCountsSum );
		$weightPow2 = $this->getWeight( $ratingCounts, $ratingCountsSum, true );
		$zScore = static::$CONFIDENCE_LEVEL_Z_SCORES[$confidencePercentage];
		return $weight - $zScore * sqrt(( $weightPow2 - pow( $weight, 2 )) / ( $ratingCountsSum + 1 ));
	}

	/**
	 * Get the bayesian ranking for an array of reviews
	 * This formula is the same one used by IMDB to rank their top 250 films
	 * @see https://www.xkcd.com/937/
	 * @see https://districtdatalabs.silvrback.com/computing-a-bayesian-estimate-of-star-rating-means
	 * @see http://fulmicoton.com/posts/bayesian_rating/
	 * @see https://stats.stackexchange.com/questions/93974/is-there-an-equivalent-to-lower-bound-of-wilson-score-confidence-interval-for-va
	 * @param int $confidencePercentage
	 * @return int|float
	 */
	public function getRankingImdb( array $reviews, $confidencePercentage = 70 )
	{
		// Represents the number of ratings expected to begin observing a pattern that would put confidence in the prior.
		$bayesMinimal = 10; // confidence
		// Represents a prior (your prior opinion without data) for the average star rating. A higher prior also means a higher margin for error.
		// This could also be the average score of all items instead of a fixed value.
		$bayesMean = ( $confidencePercentage / 100 ) * static::MAX_RATING; // prior, 70% = 3.5
		$numOfReviews = count( $reviews );
		$avgRating = $this->getAverage( $reviews );
		return $avgRating > 0
			? (( $bayesMinimal * $bayesMean ) + ( $avgRating * $numOfReviews )) / ( $bayesMinimal + $numOfReviews )
			: 0;
	}

	/**
	 * Get the sum of all review ratings
	 * @return int
	 */
	public function getTotal( array $reviews )
	{
		return array_reduce( $reviews, function( $sum, $review ) {
			return $sum + intval( $review->rating );
		});
	}

	/**
	 * @param int $target The target total percentage
	 * @return array
	 */
	protected function getRoundedPercentages( array $percentages, $target = 100 )
	{
		array_walk( $percentages, function( &$value, $index ) {
			$value = [
				'index' => $index,
				'percent' => floor( $value ),
				'remainder' => fmod( $value, 1 ),
			];
		});
		$indexes = array_column( $percentages, 'index' );
		$remainders = array_column( $percentages, 'remainder' );
		array_multisort( $remainders, SORT_DESC, SORT_STRING, $indexes, SORT_DESC, $percentages );
		$i = 0;
		if( array_sum( array_column( $percentages, 'percent' )) > 0 ) {
			while( array_sum( array_column( $percentages, 'percent' )) < $target ) {
				$percentages[$i]['percent']++;
				$i++;
			}
		}
		array_multisort( $indexes, SORT_DESC, $percentages );
		return array_combine( $indexes, array_column( $percentages, 'percent' ));
	}

	/**
	 * @param int|double $ratingCountsSum
	 * @param bool $powerOf2
	 * @return float
	 */
	protected function getWeight( array $ratingCounts, $ratingCountsSum, $powerOf2 = false )
	{
		return array_reduce( array_keys( $ratingCounts ),
			function( $count, $rating ) use( $ratingCounts, $ratingCountsSum, $powerOf2 ) {
				$ratingLevel = $powerOf2 ? pow( $rating, 2 ) : $rating;
				return $count + ( $ratingLevel * ( $ratingCounts[$rating] + 1 )) / $ratingCountsSum;
			}
		);
	}
}
