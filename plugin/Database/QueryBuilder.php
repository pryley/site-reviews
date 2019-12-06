<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Multilingual;
use GeminiLabs\SiteReviews\Modules\Rating;
use WP_Query;

class QueryBuilder
{
    /**
     * Build a WP_Query meta_query/tax_query.
     * @return array
     */
    public function buildQuery(array $keys = [], array $values = [])
    {
        $queries = [];
        foreach ($keys as $key) {
            if (!array_key_exists($key, $values)) {
                continue;
            }
            $methodName = Helper::buildMethodName($key, __FUNCTION__);
            if (!method_exists($this, $methodName)) {
                continue;
            }
            $query = call_user_func([$this, $methodName], $values[$key]);
            if (is_array($query)) {
                $queries[] = $query;
            }
        }
        return $queries;
    }

    /**
     * @return string
     */
    public function buildSqlLines(array $values, array $conditions)
    {
        $string = '';
        $values = array_filter($values);
        foreach ($conditions as $key => $value) {
            if (!isset($values[$key])) {
                continue;
            }
            $values[$key] = implode(',', (array) $values[$key]);
            $string.= Str::contains($value, '%s')
                ? sprintf($value, strval($values[$key]))
                : $value;
        }
        return $string;
    }

    /**
     * Build a SQL 'OR' string from an array.
     * @param string|array $values
     * @param string $sprintfFormat
     * @return string
     */
    public function buildSqlOr($values, $sprintfFormat)
    {
        if (!is_array($values)) {
            $values = explode(',', $values);
        }
        $values = array_filter(array_map('trim', (array) $values));
        $values = array_map(function ($value) use ($sprintfFormat) {
            return sprintf($sprintfFormat, $value);
        }, $values);
        return implode(' OR ', $values);
    }

    /**
     * Search SQL filter for matching against post title only.
     * @see http://wordpress.stackexchange.com/a/11826/1685
     * @param string $search
     * @return string
     * @filter posts_search
     */
    public function filterSearchByTitle($search, WP_Query $query)
    {
        if (empty($search) || empty($query->get('search_terms'))) {
            return $search;
        }
        global $wpdb;
        $n = empty($query->get('exact'))
            ? '%'
            : '';
        $search = [];
        foreach ((array) $query->get('search_terms') as $term) {
            $search[] = $wpdb->prepare("{$wpdb->posts}.post_title LIKE %s", $n.$wpdb->esc_like($term).$n);
        }
        if (!is_user_logged_in()) {
            $search[] = "{$wpdb->posts}.post_password = ''";
        }
        return ' AND '.implode(' AND ', $search);
    }

    /**
     * Get the current page number from the global query.
     * @param bool $isEnabled
     * @return int
     */
    public function getPaged($isEnabled = true)
    {
        return $isEnabled
            ? max(1, intval(filter_input(INPUT_GET, glsr()->constant('PAGED_QUERY_VAR'))))
            : 1;
    }

    /**
     * @param string $value
     * @return void|array
     */
    protected function buildQueryAssignedTo($value)
    {
        if (!empty($value)) {
            $postIds = Arr::convertStringToArray($value, 'is_numeric');
            return [
                'compare' => 'IN',
                'key' => '_assigned_to',
                'value' => glsr(Multilingual::class)->getPostIds($postIds),
            ];
        }
    }

    /**
     * @param array $value
     * @return void|array
     */
    protected function buildQueryCategory($value)
    {
        if (!empty($value)) {
            return [
                'field' => 'term_id',
                'taxonomy' => Application::TAXONOMY,
                'terms' => $value,
            ];
        }
    }

    /**
     * @param string $value
     * @return void|array
     */
    protected function buildQueryEmail($value)
    {
        if (!empty($value)) {
            return [
                'key' => '_email',
                'value' => $value,
            ];
        }
    }

    /**
     * @param string $value
     * @return void|array
     */
    protected function buildQueryIpAddress($value)
    {
        if (!empty($value)) {
            return [
                'key' => '_ip_address',
                'value' => $value,
            ];
        }
    }

    /**
     * @param string $value
     * @return void|array
     */
    protected function buildQueryRating($value)
    {
        if (is_numeric($value)
            && in_array(intval($value), range(1, glsr()->constant('MAX_RATING', Rating::class)))) {
            return [
                'compare' => '>=',
                'key' => '_rating',
                'value' => $value,
            ];
        }
    }

    /**
     * @param string $value
     * @return void|array
     */
    protected function buildQueryType($value)
    {
        if (!in_array($value, ['', 'all'])) {
            return [
                'key' => '_review_type',
                'value' => $value,
            ];
        }
    }
}
