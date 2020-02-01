<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Multilingual;
use GeminiLabs\SiteReviews\Modules\Rating;
use GeminiLabs\SiteReviews\Review;
use WP_Post;

class CountsManager
{
    const LIMIT = 500;
    const META_AVERAGE = '_glsr_average';
    const META_COUNT = '_glsr_count';
    const META_RANKING = '_glsr_ranking';

    /**
     * @return array
     */
    public function buildCounts(array $args = [])
    {
        $counts = [
            'local' => $this->generateEmptyCountsArray(),
        ];
        $query = $this->queryReviews($args);
        while ($query) {
            $counts = $this->populateCountsFromQuery($query, $counts);
            $query = $query->has_more
                ? $this->queryReviews($args, end($query->reviews)->ID)
                : false;
        }
        return $counts;
    }

    /**
     * @return void
     */
    public function decreaseAll(Review $review)
    {
        glsr(GlobalCountsManager::class)->decrease($review);
        glsr(PostCountsManager::class)->decrease($review);
        glsr(TermCountsManager::class)->decrease($review);
    }

    /**
     * @param string $type
     * @param int $rating
     * @return array
     */
    public function decreaseRating(array $reviewCounts, $type, $rating)
    {
        if (isset($reviewCounts[$type][$rating])) {
            $reviewCounts[$type][$rating] = max(0, $reviewCounts[$type][$rating] - 1);
        }
        return $reviewCounts;
    }

    /**
     * @return array
     */
    public function flatten(array $reviewCounts, array $args = [])
    {
        $counts = [];
        array_walk_recursive($reviewCounts, function ($num, $index) use (&$counts) {
            $counts[$index] = $num + intval(Arr::get($counts, $index, 0));
        });
        $min = Arr::get($args, 'min', glsr()->constant('MIN_RATING', Rating::class));
        $max = Arr::get($args, 'max', glsr()->constant('MAX_RATING', Rating::class));
        foreach ($counts as $index => &$num) {
            if (!Helper::inRange($index, $min, $max)) {
                $num = 0;
            }
        }
        return $counts;
    }

    /**
     * @return array
     */
    public function getCounts(array $args = [])
    {
        $args = $this->normalizeArgs($args);
        $counts = $this->hasMixedAssignment($args)
            ? $this->buildCounts($args) // force query the database
            : $this->get($args);
        return $this->normalize($counts);
    }

    /**
     * @return void
     */
    public function increaseAll(Review $review)
    {
        glsr(GlobalCountsManager::class)->increase($review);
        glsr(PostCountsManager::class)->increase($review);
        glsr(TermCountsManager::class)->increase($review);
    }

    /**
     * @param string $type
     * @param int $rating
     * @return array
     */
    public function increaseRating(array $reviewCounts, $type, $rating)
    {
        if (!array_key_exists($type, glsr()->reviewTypes)) {
            return $reviewCounts;
        }
        if (!array_key_exists($type, $reviewCounts)) {
            $reviewCounts[$type] = [];
        }
        $reviewCounts = $this->normalize($reviewCounts);
        $reviewCounts[$type][$rating] = intval($reviewCounts[$type][$rating]) + 1;
        return $reviewCounts;
    }

    /**
     * @return void
     */
    public function updateAll()
    {
        glsr(GlobalCountsManager::class)->updateAll();
        glsr(PostCountsManager::class)->updateAll();
        glsr(TermCountsManager::class)->updateAll();
        glsr(OptionManager::class)->set('last_review_count', current_time('timestamp'));
    }

    /**
     * @return array
     */
    protected function combine(array $results)
    {
        if (!wp_is_numeric_array($results)) {
            return $results;
        }
        $mergedKeys = array_keys(array_merge(...$results));
        $counts = array_fill_keys($mergedKeys, $this->generateEmptyCountsArray());
        foreach ($results as $typeRatings) {
            foreach ($typeRatings as $type => $ratings) {
                foreach ($ratings as $index => $rating) {
                    $counts[$type][$index] = intval($rating) + $counts[$type][$index];
                }
            }
        }
        return $counts;
    }

    /**
     * @return array
     */
    protected function generateEmptyCountsArray()
    {
        return array_fill_keys(range(0, glsr()->constant('MAX_RATING', Rating::class)), 0);
    }

    /**
     * @return array
     */
    protected function get($args)
    {
        $results = [];
        foreach ($args['post_ids'] as $postId) {
            $results[] = glsr(PostCountsManager::class)->get($postId);
        }
        foreach ($args['term_ids'] as $termId) {
            $results[] = glsr(TermCountsManager::class)->get($termId);
        }
        if (empty($results)) {
            $results[] = glsr(GlobalCountsManager::class)->get();
        }
        $results[] = ['local' => $this->generateEmptyCountsArray()]; // make sure there is a fallback
        return $this->combine($results);
    }

    /**
     * @return bool
     */
    protected function hasMixedAssignment(array $args)
    {
        return !empty($args['post_ids']) && !empty($args['term_ids']);
    }

    /**
     * @return array
     */
    protected function normalize(array $reviewCounts)
    {
        foreach ($reviewCounts as &$counts) {
            foreach (array_keys($this->generateEmptyCountsArray()) as $index) {
                if (!isset($counts[$index])) {
                    $counts[$index] = 0;
                }
            }
            ksort($counts);
        }
        return $reviewCounts;
    }

    /**
     * @return array
     */
    protected function normalizeArgs(array $args)
    {
        $args = wp_parse_args(array_filter($args), [
            'post_ids' => [],
            'term_ids' => [],
            'type' => 'local',
        ]);
        $args['post_ids'] = glsr(Multilingual::class)->getPostIds($args['post_ids']);
        $args['type'] = $this->normalizeType($args['type']);
        return $args;
    }

    /**
     * @param string $type
     * @return string
     */
    protected function normalizeType($type)
    {
        return empty($type) || !is_string($type)
            ? 'local'
            : $type;
    }

    /**
     * @param object $query
     * @return array
     */
    protected function populateCountsFromQuery($query, array $counts)
    {
        foreach ($query->reviews as $review) {
            $type = $this->normalizeType($review->type);
            if (!array_key_exists($type, $counts)) {
                $counts[$type] = $this->generateEmptyCountsArray();
            }
            ++$counts[$type][$review->rating];
        }
        return $counts;
    }

    /**
     * @param int $lastPostId
     * @return object
     */
    protected function queryReviews(array $args = [], $lastPostId = 0)
    {
        $reviews = glsr(SqlQueries::class)->getReviewCounts($args, $lastPostId, static::LIMIT);
        $hasMore = is_array($reviews)
            ? count($reviews) == static::LIMIT
            : false;
        return (object) [
            'has_more' => $hasMore,
            'reviews' => $reviews,
        ];
    }
}
