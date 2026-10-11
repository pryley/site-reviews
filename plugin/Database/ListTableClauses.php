<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Defaults\ColumnFilterbyDefaults;
use GeminiLabs\SiteReviews\Defaults\ColumnOrderbyDefaults;
use GeminiLabs\SiteReviews\Defaults\SqlClauseDefaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;

/**
 * The SQL clauses that handle the listtable filters and column sorting.
 */
class ListTableClauses
{
    protected array $filterBy;
    protected string $posts;
    protected \WP_Query $query;
    protected string $ratings;

    public function __construct(\WP_Query $query)
    {
        $filterBy = glsr(ColumnFilterbyDefaults::class)->defaults();
        $filterBy = filter_input_array(\INPUT_GET, $filterBy);
        $this->filterBy = Arr::removeEmptyValues(Arr::consolidate($filterBy));
        $this->posts = glsr(Tables::class)->table('posts');
        $this->query = $query;
        $this->ratings = glsr(Tables::class)->table('ratings');
    }

    public function isFiltered(): bool
    {
        return !empty($this->filterBy);
    }

    public function isOrdered(): bool
    {
        $columns = glsr(ColumnOrderbyDefaults::class)->defaults();
        $column = Cast::toString(get_query_var('orderby')); // get_query_var output is unpredictable
        return array_key_exists($column, $columns);
    }

    public function join(string $join): array
    {
        $clauses = ["INNER JOIN {$this->ratings} ON ({$this->ratings}.review_id = {$this->posts}.ID)"];
        foreach ($this->filterBy as $key => $value) {
            if (!in_array($key, ['assigned_post', 'assigned_user'])) {
                continue;
            }
            $assignedTable = glsr(Tables::class)->table($key.'s');
            $type = 0 === Cast::toInt($value) ? 'LEFT' : 'INNER'; // "none" needs the rows that have no row there
            $clauses[] = "{$type} JOIN {$assignedTable} ON ({$assignedTable}.rating_id = {$this->ratings}.ID)";
        }
        return $this->clause($clauses, false);
    }

    public function orderby(string $orderby): array
    {
        $columns = glsr(ColumnOrderbyDefaults::class)->defaults();
        $column = Arr::get($columns, $this->query->get('orderby'));
        if (empty($column)) {
            return $this->clause([], true);
        }
        if ($this->isOrderbyWithIsNull($column)) {
            return $this->clause(["NULLIF({$this->ratings}.{$column}, '') IS NULL, {$orderby}"], true);
        }
        return $this->clause(["{$this->ratings}.{$column} {$this->query->get('order')}"], true);
    }

    public function where(string $where): array
    {
        $clauses = [];
        foreach ($this->filterBy as $key => $value) {
            $method = Helper::buildMethodName('where', $key);
            if (method_exists($this, $method)) {
                $clauses[] = $this->$method(Cast::toString($value));
            }
        }
        return $this->clause(array_filter($clauses), false);
    }

    protected function clause(array $clauses, bool $replace): array
    {
        return glsr(SqlClauseDefaults::class)->restrict(compact('clauses', 'replace'));
    }

    protected function isOrderbyWithIsNull(string $column): bool
    {
        $columns = [
            'email', 'name', 'ip_address', 'type',
        ];
        $columns = glsr()->filterArray('columns/orderby-is-null', $columns);
        return in_array($column, $columns);
    }

    protected function whereAssigned(string $key, string $value): string
    {
        $assignedTable = glsr(Tables::class)->table($key.'s');
        $column = Str::suffix(Str::removePrefix($key, 'assigned_'), '_id');
        $value = Cast::toInt($value);
        if (0 === $value) {
            return "AND {$assignedTable}.{$column} IS NULL";
        }
        return "AND {$assignedTable}.{$column} = {$value}";
    }

    protected function whereAssignedPost(string $value): string
    {
        return $this->whereAssigned('assigned_post', $value);
    }

    protected function whereAssignedUser(string $value): string
    {
        return $this->whereAssigned('assigned_user', $value);
    }

    protected function whereAuthor(string $value): string
    {
        return '0' === $value ? "AND {$this->posts}.post_author IN (0)" : '';
    }

    protected function whereRating(string $value): string
    {
        return "AND {$this->ratings}.rating = '{$value}'";
    }

    protected function whereTerms(string $value): string
    {
        return "AND {$this->ratings}.terms = '{$value}'";
    }

    protected function whereType(string $value): string
    {
        return "AND {$this->ratings}.type = '{$value}'";
    }
}
