<?php

namespace GeminiLabs\SiteReviews\Modules;

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
	const CONFIDENCE_LEVEL_Z_SCORES = [
		50 => 0.67449,
		70 => 1.04,
		75 => 1.15035,
		80 => 1.282,
		85 => 1.44,
		90 => 1.64485,
		92 => 1.75,
		95 => 1.95996,
		96 => 2.05,
		97 => 2.17009,
		98 => 2.326,
		99 => 2.57583,
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
	 * @param int $roundBy
	 * @return float
	 */
	public function getAverage( array $ratingCounts, $roundBy = 1 )
	{
		$average = array_sum( $ratingCounts );
		if( $average > 0 ) {
			$average = round( $this->getTotalSum( $ratingCounts ) / $average, intval( $roundBy ));
		}
		return floatval( apply_filters( 'site-reviews/rating/average', $average, $ratingCounts ));
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
	public function getLowerBound( array $upDownCounts = [0, 0], $confidencePercentage = 95 )
	{
		$numRatings = array_sum( $upDownCounts );
		if( $numRatings < 1 ) {
			return 0;
		}
		$z = static::CONFIDENCE_LEVEL_Z_SCORES[$confidencePercentage];
		$phat = 1 * $upDownCounts[1] / $numRatings;
		return ( $phat + $z * $z / ( 2 * $numRatings ) - $z * sqrt(( $phat * ( 1 - $phat ) + $z * $z / ( 4 * $numRatings )) / $numRatings )) / ( 1 + $z * $z / $numRatings );
	}

	/**
	 * @return int|float
	 */
	public function getOverallPercentage( array $ratingCounts )
	{
		return round( $this->getAverage( $ratingCounts ) * 100 / static::MAX_RATING, 2 );
	}

	/**
	 * @return array
	 */
	public function getPercentages( array $ratingCounts )
	{
		$total = array_sum( $ratingCounts );
		foreach( $ratingCounts as $index => $count ) {
			if( empty( $count ))continue;
			$ratingCounts[$index] = $count / $total * 100;
		}
		return $this->getRoundedPercentages( $ratingCounts );
	}

	/**
	 * @return float
	 */
	public function getRanking( array $ratingCounts )
	{
		return floatval( apply_filters( 'site-reviews/rating/ranking',
			$this->getRankingUsingImdb( $ratingCounts ),
			$ratingCounts,
			$this
		));
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
	public function getRankingUsingImdb( array $ratingCounts, $confidencePercentage = 70 )
	{
		$avgRating = $this->getAverage( $ratingCounts );
		// Represents a prior (your prior opinion without data) for the average star rating. A higher prior also means a higher margin for error.
		// This could also be the average score of all items instead of a fixed value.
		$bayesMean = ( $confidencePercentage / 100 ) * static::MAX_RATING; // prior, 70% = 3.5
		// Represents the number of ratings expected to begin observing a pattern that would put confidence in the prior.
		$bayesMinimal = 10; // confidence
		$numOfReviews = array_sum( $ratingCounts );
		return $avgRating > 0
			? (( $bayesMinimal * $bayesMean ) + ( $avgRating * $numOfReviews )) / ( $bayesMinimal + $numOfReviews )
			: 0;
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
	public function getRankingUsingZScores( array $ratingCounts, $confidencePercentage = 90 )
	{
		$ratingCountsSum = array_sum( $ratingCounts ) + static::MAX_RATING;
		$weight = $this->getWeight( $ratingCounts, $ratingCountsSum );
		$weightPow2 = $this->getWeight( $ratingCounts, $ratingCountsSum, true );
		$zScore = static::CONFIDENCE_LEVEL_Z_SCORES[$confidencePercentage];
		return $weight - $zScore * sqrt(( $weightPow2 - pow( $weight, 2 )) / ( $ratingCountsSum + 1 ));
	}

	/**
	 * @param int $target
	 * @return array
	 */
	protected function getRoundedPercentages( array $percentages, $totalPercent = 100 )
	{
		array_walk( $percentages, function( &$percent, $index ) {
			$percent = [
				'index' => $index,
				'percent' => floor( $percent ),
				'remainder' => fmod( $percent, 1 ),
			];
		});
		$indexes = glsr_array_column( $percentages, 'index' );
		$remainders = glsr_array_column( $percentages, 'remainder' );
		array_multisort( $remainders, SORT_DESC, SORT_STRING, $indexes, SORT_DESC, $percentages );
		$i = 0;
		if( array_sum( glsr_array_column( $percentages, 'percent' )) > 0 ) {
			while( array_sum( glsr_array_column( $percentages, 'percent' )) < $totalPercent ) {
				$percentages[$i]['percent']++;
				$i++;
			}
		}
		array_multisort( $indexes, SORT_DESC, $percentages );
		return array_combine( $indexes, glsr_array_column( $percentages, 'percent' ));
	}

	/**
	 * @return int
	 */
	protected function getTotalSum( array $ratingCounts )
	{
		return array_reduce( array_keys( $ratingCounts ), function( $carry, $index ) use( $ratingCounts ) {
			return $carry + ( $index * $ratingCounts[$index] );
		});
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
				$ratingLevel = $powerOf2
					? pow( $rating, 2 )
					: $rating;
				return $count + ( $ratingLevel * ( $ratingCounts[$rating] + 1 )) / $ratingCountsSum;
			}
		);
	}
}
