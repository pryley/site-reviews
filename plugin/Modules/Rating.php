<?php

namespace GeminiLabs\SiteReviews\Modules;

class Rating
{
    public const CONFIDENCE_LEVEL_Z_SCORES = [
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
    public const MAX_RATING = 5;
    public const MIN_RATING = 0;

    /**
     * @param int[] $ratingCounts
     */
    public function average(array $ratingCounts, ?int $roundBy = null): float
    {
        $average = 0;
        $total = $this->totalCount($ratingCounts);
        if ($total > 0) {
            $average = $this->totalSum($ratingCounts) / $total;
        }
        if (is_null($roundBy)) {
            $roundBy = glsr()->filterInt('rating/round-by', 1);
        }
        $roundedAverage = round($average, intval($roundBy));
        return glsr()->filterFloat('rating/average', $roundedAverage, $average, $ratingCounts);
    }

    public function emptyArray(): array
    {
        return array_fill_keys(range(0, glsr()->constant('MAX_RATING', __CLASS__)), 0);
    }

    public function format(float $rating): string
    {
        $roundBy = glsr()->filterInt('rating/round-by', 1);
        return (string) number_format_i18n($rating, $roundBy);
    }

    public function isValid(int $rating): bool
    {
        return array_key_exists($rating, $this->emptyArray());
    }

    /**
     * Get the lower bound for up/down ratings
     * Method receives an up/down ratings array: [1, -1, -1, 1, 1, -1].
     *
     * @see http://www.evanmiller.org/how-not-to-sort-by-average-rating.html
     * @see https://news.ycombinator.com/item?id=10481507
     * @see https://dataorigami.net/blogs/napkin-folding/79030467-an-algorithm-to-sort-top-comments
     * @see http://julesjacobs.github.io/2015/08/17/bayesian-scoring-of-ratings.html
     */
    public function lowerBound(array $upDownCounts = [0, 0], int $confidencePercentage = 95): float
    {
        $numRatings = array_sum($upDownCounts);
        if ($numRatings < 1) {
            return 0;
        }
        $z = static::CONFIDENCE_LEVEL_Z_SCORES[$confidencePercentage];
        $phat = 1 * $upDownCounts[1] / $numRatings;
        return (float) ($phat + $z * $z / (2 * $numRatings) - $z * sqrt(($phat * (1 - $phat) + $z * $z / (4 * $numRatings)) / $numRatings)) / (1 + $z * $z / $numRatings);
    }

    /**
     * @param array $noopedPlural The result of _n_noop()
     */
    public function optionsArray(array $noopedPlural = [], int $minRating = 1): array
    {
        $options = [];
        if (empty($noopedPlural)) {
            $noopedPlural = _n_noop('%s Star', '%s Stars', 'site-reviews');
        }
        foreach (range(glsr()->constant('MAX_RATING', __CLASS__), $minRating) as $rating) {
            $title = translate_nooped_plural($noopedPlural, $rating, 'site-reviews');
            if (!str_contains($title, '%s')) {
                $title = "%s {$title}"; // because Arr::unique() is used for array values when defaults are merged.
            }
            $options[$rating] = wp_sprintf($title, $rating);
        }
        return $options;
    }

    /**
     * @param int[] $ratingCounts
     */
    public function overallPercentage(array $ratingCounts): float
    {
        return round($this->average($ratingCounts) * 100 / glsr()->constant('MAX_RATING', __CLASS__), 2);
    }

    /**
     * @param int[] $ratingCounts
     */
    public function percentages(array $ratingCounts): array
    {
        if (empty($ratingCounts)) {
            $ratingCounts = $this->emptyArray();
        }
        $percentages = [];
        $total = array_sum($ratingCounts);
        foreach ($ratingCounts as $index => $count) {
            $percentage = empty($count) ? 0 : $count / $total * 100;
            $percentages[$index] = (float) $percentage;
        }
        return $this->roundedPercentages($percentages);
    }

    /**
     * @param int[] $ratingCounts
     */
    public function ranking(array $ratingCounts): float
    {
        return glsr()->filterFloat('rating/ranking',
            $this->rankingUsingImdb($ratingCounts),
            $ratingCounts,
            $this
        );
    }

    /**
     * Get the bayesian ranking for an array of reviews
     * This formula is the same one used by IMDB to rank their top 250 films.
     *
     * @see https://www.xkcd.com/937/
     * @see https://districtdatalabs.silvrback.com/computing-a-bayesian-estimate-of-star-rating-means
     * @see http://fulmicoton.com/posts/bayesian_rating/
     * @see https://stats.stackexchange.com/questions/93974/is-there-an-equivalent-to-lower-bound-of-wilson-score-confidence-interval-for-va
     */
    public function rankingUsingImdb(array $ratingCounts, int $confidencePercentage = 70): float
    {
        $avgRating = $this->average($ratingCounts);
        // Represents a prior (your prior opinion without data) for the average star rating. A higher prior also means a higher margin for error.
        // This could also be the average score of all items instead of a fixed value.
        $bayesMean = ($confidencePercentage / 100) * glsr()->constant('MAX_RATING', __CLASS__); // prior, 70% = 3.5
        // Represents the number of ratings expected to begin observing a pattern that would put confidence in the prior.
        $bayesMinimal = 10; // confidence
        $numOfReviews = $this->totalCount($ratingCounts);
        return $avgRating > 0
            ? (float) (($bayesMinimal * $bayesMean) + ($avgRating * $numOfReviews)) / ($bayesMinimal + $numOfReviews)
            : (float) 0;
    }

    /**
     * The quality of a 5 star rating depends not only on the average number of stars but also on
     * the number of reviews. This method calculates the bayesian ranking of a page by its number
     * of reviews and their rating.
     *
     * @see http://www.evanmiller.org/ranking-items-with-star-ratings.html
     * @see https://stackoverflow.com/questions/1411199/what-is-a-better-way-to-sort-by-a-5-star-rating/1411268
     * @see http://julesjacobs.github.io/2015/08/17/bayesian-scoring-of-ratings.html
     *
     * @param int[] $ratingCounts
     */
    public function rankingUsingZScores(array $ratingCounts, int $confidencePercentage = 90): float
    {
        $ratingCountsSum = (float) $this->totalCount($ratingCounts) + glsr()->constant('MAX_RATING', __CLASS__);
        $weight = $this->weight($ratingCounts, $ratingCountsSum);
        $weightPow2 = $this->weight($ratingCounts, $ratingCountsSum, true);
        $zScore = static::CONFIDENCE_LEVEL_Z_SCORES[$confidencePercentage];
        return $weight - $zScore * sqrt(($weightPow2 - pow($weight, 2)) / ($ratingCountsSum + 1));
    }

    /**
     * @param int[] $ratingCounts
     */
    public function totalCount(array $ratingCounts): int
    {
        $values = array_filter($ratingCounts, 'is_numeric');
        $values = array_map('intval', $values);
        if (isset($values[0]) && glsr()->filterBool('rating/ignore-zero-stars', true)) {
            $values[0] = 0; // ignore 0-star ratings when calculating the average and ranking
        }
        return (int) array_sum($values);
    }

    /**
     * Returns array sorted by key DESC.
     *
     * @param float[] $percentages
     */
    protected function roundedPercentages(array $percentages, int $totalPercent = 100): array
    {
        array_walk($percentages, function (&$percent, $index) {
            $percent = [
                'index' => $index,
                'percent' => floor($percent),
                'remainder' => fmod($percent, 1),
            ];
        });
        $indexes = wp_list_pluck($percentages, 'index');
        $remainders = wp_list_pluck($percentages, 'remainder');
        array_multisort($remainders, SORT_DESC, SORT_STRING, $indexes, SORT_DESC, $percentages);
        $i = 0;
        if (array_sum(wp_list_pluck($percentages, 'percent')) > 0) {
            while (array_sum(wp_list_pluck($percentages, 'percent')) < $totalPercent) {
                ++$percentages[$i]['percent'];
                ++$i;
            }
        }
        array_multisort($indexes, SORT_DESC, $percentages);
        return array_combine($indexes, wp_list_pluck($percentages, 'percent'));
    }

    /**
     * @param int[] $ratingCounts
     */
    protected function totalSum(array $ratingCounts): int
    {
        return (int) array_reduce(
            array_keys($ratingCounts),
            fn ($carry, $i) => $carry + ($i * $ratingCounts[$i]),
            0
        );
    }

    /**
     * @param int[] $ratingCounts
     */
    protected function weight(array $ratingCounts, float $ratingCountsSum, bool $powerOf2 = false): float
    {
        return (float) array_reduce(array_keys($ratingCounts),
            function ($count, $rating) use ($ratingCounts, $ratingCountsSum, $powerOf2) {
                $ratingLevel = $powerOf2
                    ? pow($rating, 2)
                    : $rating;
                return $count + ($ratingLevel * ($ratingCounts[$rating] + 1)) / $ratingCountsSum;
            },
            0
        );
    }
}
