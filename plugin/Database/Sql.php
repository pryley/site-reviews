<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;

trait Sql
{
    /** @var array */
    public $args;

    public \wpdb $db;

    public function clauses(string $clause, array $values = []): array
    {
        $prefix = Str::restrictTo('and,join', $clause);
        foreach ($this->args as $key => $value) {
            $method = Helper::buildMethodName('clause', $prefix, $key);
            if (!method_exists($this, $method) || Helper::isEmpty($value)) {
                continue;
            }
            if ($statement = call_user_func([$this, $method])) {
                $values[$key] = $statement;
            }
        }
        return $values;
    }

    public function escFieldsForInsert(array $fields): string
    {
        return sprintf('(`%s`)', implode('`,`', $fields));
    }

    public function escValuesForInsert(array $values): string
    {
        $values = array_values(
            array_map('\GeminiLabs\SiteReviews\Helpers\Cast::toString', array_map('esc_sql', $values))
        );
        return sprintf("('%s')", implode("','", $values));
    }

    /**
     * This method allows the following SQL syntax:
     *  - ALTER TABLE table|<unprefixed_table_name>
     *  - FROM table|<unprefixed_table_name>
     *  - JOIN table|<unprefixed_table_name>
     *  - TRUNCATE TABLE table|<unprefixed_table_name>
     *  - UPDATE table|<unprefixed_table_name>
     *
     * @param string|int ...$args Additional parameters will be passed to $wpdb->prepare()
     */
    public function sql(string $statement, ...$args): string
    {
        $handle = $this->sqlHandle();
        $statement = preg_replace('/ {12}/', '', $statement);
        $statement = glsr()->filterString("database/sql/{$handle}", $statement);
        $statement = preg_replace_callback('/(ALTER TABLE|FROM|JOIN|TRUNCATE TABLE|UPDATE)(\s+)(table\|)([^\s]+)/',
            fn ($m) => $m[1].$m[2].glsr(Tables::class)->table($m[4]),
            $statement
        );
        if (!empty($args)) {
            $statement = $this->db->prepare($statement, ...$args);
        }
        glsr()->action("database/sql/{$handle}", $statement);
        glsr()->action('database/sql', $statement, $handle);
        return $statement;
    }

    public function sqlJoin(): string
    {
        $join = $this->clauses('join');
        $join = glsr()->filterArrayUnique('query/sql/join', $join, $this->sqlHandle(), $this);
        return implode(' ', $join);
    }

    public function sqlLimit(): string
    {
        $limit = Helper::ifTrue($this->args['per_page'] > 0,
            $this->db->prepare('LIMIT %d', $this->args['per_page'])
        );
        return glsr()->filterString('query/sql/limit', $limit, $this->sqlHandle(), $this);
    }

    public function sqlOffset(): string
    {
        $offsetBy = (($this->args['page'] - 1) * $this->args['per_page']) + $this->args['offset'];
        $offset = Helper::ifTrue($offsetBy > 0,
            $this->db->prepare('OFFSET %d', $offsetBy)
        );
        return glsr()->filterString('query/sql/offset', $offset, $this->sqlHandle(), $this);
    }

    public function sqlOrderBy(): string
    {
        $values = [
            'random' => 'RAND()',
        ];
        $order = $this->args['order'];
        $orderby = $this->args['orderby'];
        $orderedby = [];
        if (Str::startsWith($orderby, ['p.', 'r.'])) {
            $orderedby[] = "r.is_pinned {$order}";
            $orderedby[] = "{$orderby} {$order}";
        } elseif (array_key_exists($orderby, $values)) {
            $orderedby[] = $values[$orderby];
        }
        $orderedby = glsr()->filterArrayUnique('query/sql/order-by', $orderedby, $this->sqlHandle(), $this);
        if (empty($orderedby)) {
            return '';
        }
        return 'ORDER BY '.implode(', ', $orderedby);
    }

    public function sqlWhere(): string
    {
        $and = $this->clauses('and');
        $and = glsr()->filterArrayUnique('query/sql/and', $and, $this->sqlHandle(), $this);
        $and = $this->normalizeAndClauses($and);
        return 'WHERE 1=1 '.implode(' ', $and);
    }

    protected function clauseAndAssignedPosts(): string
    {
        return $this->clauseIfValueNotEmpty('(apt.post_id IN (%s) AND apt.is_published = 1)', $this->args['assigned_posts']);
    }

    protected function clauseAndAssignedPostsTypes(): string
    {
        return $this->clauseIfValueNotEmpty('apt.is_published = 1', $this->args['assigned_posts_types']);
    }

    protected function clauseAndAssignedTerms(): string
    {
        return $this->clauseIfValueNotEmpty('(att.term_id IN (%s))', $this->args['assigned_terms']);
    }

    protected function clauseAndAssignedUsers(): string
    {
        return $this->clauseIfValueNotEmpty('(aut.user_id IN (%s))', $this->args['assigned_users']);
    }

    protected function clauseAndContent(): string
    {
        return $this->clauseIfValueNotEmpty('AND p.post_content = %s', $this->args['content']);
    }

    protected function clauseAndDate(): string
    {
        $clauses = [];
        $date = $this->args['date'];
        if (!empty($date['after'])) {
            $clauses[] = $this->db->prepare("(p.post_date >{$date['inclusive']} %s)", $date['after']);
        }
        if (!empty($date['before'])) {
            $clauses[] = $this->db->prepare("(p.post_date <{$date['inclusive']} %s)", $date['before']);
        }
        if (!empty($date['year'])) {
            $clauses[] = $this->db->prepare('(YEAR(p.post_date) = %d AND MONTH(p.post_date) = %d AND DAYOFMONTH(p.post_date) = %d)',
                $date['year'], $date['month'], $date['day']
            );
        }
        if ($clauses = implode(' AND ', $clauses)) {
            return sprintf('AND (%s)', $clauses);
        }
        return '';
    }

    protected function clauseAndEmail(): string
    {
        return $this->clauseIfValueNotEmpty('AND r.email = %s', $this->args['email']);
    }

    protected function clauseAndIpAddress(): string
    {
        return $this->clauseIfValueNotEmpty('AND r.ip_address = %s', $this->args['ip_address']);
    }

    protected function clauseAndPostIn(): string
    {
        return $this->clauseIfValueNotEmpty('AND r.review_id IN (%s)', $this->args['post__in']);
    }

    protected function clauseAndPostNotIn(): string
    {
        return $this->clauseIfValueNotEmpty('AND r.review_id NOT IN (%s)', $this->args['post__not_in']);
    }

    protected function clauseAndRating(): string
    {
        $column = $this->isCustomRatingField() ? 'pm.meta_value' : 'r.rating';
        return (string) Helper::ifTrue($this->args['rating'] > 0,
            $this->db->prepare("AND {$column} > %d", --$this->args['rating'])
        );
    }

    protected function clauseAndRatingField(): string
    {
        return (string) Helper::ifTrue($this->isCustomRatingField(),
            $this->db->prepare('AND pm.meta_key = %s', sprintf('_custom_%s', $this->args['rating_field']))
        );
    }

    protected function clauseAndStatus(): string
    {
        if (-1 !== $this->args['status']) {
            return $this->clauseIfValueNotEmpty('AND r.is_approved = %d', $this->args['status']);
        }
        return "AND p.post_status IN ('pending','publish')";
    }

    protected function clauseAndTerms(): string
    {
        if (-1 !== $this->args['terms']) {
            return $this->clauseIfValueNotEmpty('AND r.terms = %d', $this->args['terms']);
        }
        return '';
    }

    protected function clauseAndType(): string
    {
        return $this->clauseIfValueNotEmpty('AND r.type = %s', $this->args['type']);
    }

    protected function clauseAndUserIn(): string
    {
        return $this->clauseIfValueNotEmpty('AND p.post_author IN (%s)', $this->args['user__in']);
    }

    protected function clauseAndUserNotIn(): string
    {
        return $this->clauseIfValueNotEmpty('AND p.post_author NOT IN (%s)', $this->args['user__not_in']);
    }

    /**
     * @param array|int|string $value
     */
    protected function clauseIfValueNotEmpty(string $clause, $value, bool $prepare = true): string
    {
        if (Helper::isEmpty($value)) {
            return '';
        }
        if (!$prepare) {
            return $clause;
        }
        if (is_array($value)) {
            $value = implode(',', Arr::uniqueInt($value));
            return sprintf($clause, $value); // this clause uses IN(%s) so we need to bypass db->prepare
        }
        return $this->db->prepare($clause, $value);
    }

    protected function clauseJoinAssignedPosts(): string
    {
        return $this->clauseIfValueNotEmpty(
            "{$this->joinMethod()} table|assigned_posts AS apt ON (apt.rating_id = r.ID)",
            $this->args['assigned_posts'],
            $prepare = false
        );
    }

    protected function clauseJoinAssignedPostsTypes(): string
    {
        $clause1 = "{$this->joinMethod()} table|assigned_posts AS apt ON (apt.rating_id = r.ID)";
        $clause2 = "INNER JOIN table|posts AS pt ON (pt.ID = apt.post_id AND pt.post_type IN ('%s'))";
        $values = Arr::unique($this->args['assigned_posts_types']);
        $values = array_map('esc_sql', $values);
        $values = array_filter($values, 'is_string'); // for phpstan
        $values = implode("','", $values);
        return sprintf(sprintf('%s %s', $clause1, $clause2), $values);
    }

    protected function clauseJoinAssignedTerms(): string
    {
        return $this->clauseIfValueNotEmpty(
            "{$this->joinMethod()} table|assigned_terms AS att ON (att.rating_id = r.ID)",
            $this->args['assigned_terms'],
            $prepare = false
        );
    }

    protected function clauseJoinAssignedUsers(): string
    {
        return $this->clauseIfValueNotEmpty(
            "{$this->joinMethod()} table|assigned_users AS aut ON (aut.rating_id = r.ID)",
            $this->args['assigned_users'],
            $prepare = false
        );
    }

    protected function clauseJoinContent(): string
    {
        return $this->clauseIfValueNotEmpty(
            "INNER JOIN table|posts AS p ON (p.ID = r.review_id)",
            $this->args['content'],
            $prepare = false
        );
    }

    protected function clauseJoinDate(): string
    {
        return $this->clauseIfValueNotEmpty(
            "INNER JOIN table|posts AS p ON (p.ID = r.review_id)",
            array_filter($this->args['date']),
            $prepare = false
        );
    }

    protected function clauseJoinUserIn(): string
    {
        return $this->clauseIfValueNotEmpty(
            "INNER JOIN table|posts AS p ON (p.ID = r.review_id)",
            $this->args['user__in'],
            $prepare = false
        );
    }

    protected function clauseJoinUserNotIn(): string
    {
        return $this->clauseIfValueNotEmpty(
            "INNER JOIN table|posts AS p ON (p.ID = r.review_id)",
            $this->args['user__not_in'],
            $prepare = false
        );
    }

    protected function clauseJoinOrderBy(): string
    {
        return (string) Helper::ifTrue(str_starts_with($this->args['orderby'], 'p.'),
            "INNER JOIN table|posts AS p ON (p.ID = r.review_id)"
        );
    }

    protected function clauseJoinRatingField(): string
    {
        return (string) Helper::ifTrue($this->isCustomRatingField(),
            "INNER JOIN table|postmeta AS pm ON (pm.post_id = r.review_id)"
        );
    }

    protected function clauseJoinStatus(): string
    {
        return (string) Helper::ifTrue(-1 === $this->args['status'],
            "INNER JOIN table|posts AS p ON (p.ID = r.review_id)"
        );
    }

    protected function isCustomRatingField(): bool
    {
        return 'rating' !== $this->args['rating_field'] && !empty($this->args['rating_field']);
    }

    protected function joinMethod(): string
    {
        $joins = ['loose' => 'LEFT JOIN', 'strict' => 'INNER JOIN'];
        return Arr::get($joins, glsr_get_option('reviews.assignment', 'strict'), 'INNER JOIN');
    }

    protected function normalizeAndClauses(array $and): array
    {
        $clauses = [];
        foreach ($and as $key => $value) {
            if (str_starts_with($key, 'assigned_')) {
                $clauses[] = $value;
                unset($and[$key]);
            }
        }
        $operator = glsr()->filterString('query/sql/clause/operator', 'OR', $clauses, $this->args);
        $operator = strtoupper($operator);
        $operator = Helper::ifTrue(in_array($operator, ['AND', 'OR']), $operator, 'OR');
        if ($clauses = implode(" {$operator} ", $clauses)) {
            $and['assigned'] = "AND ($clauses)";
        }
        return $and;
    }

    protected function ratingColumn(): string
    {
        return Helper::ifTrue($this->isCustomRatingField(), 'pm.meta_value', 'r.rating');
    }

    protected function sqlHandle(int $depth = 2): string
    {
        return Str::dashCase(Arr::get((new \Exception())->getTrace(), $depth.'.function'));
    }
}
