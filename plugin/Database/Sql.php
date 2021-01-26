<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;

trait Sql
{
    /**
     * @var array
     */
    public $args;

    /**
     * @var \wpdb
     */
    public $db;

    /**
     * @param string $clause
     * @return array
     */
    public function clauses($clause, array $values = [])
    {
        $prefix = Str::restrictTo('and,join', $clause);
        foreach (array_keys($this->args) as $key) {
            $method = Helper::buildMethodName($key, 'clause-'.$prefix);
            if (method_exists($this, $method)) {
                $values[$key] = call_user_func([$this, $method]);
            }
        }
        return $values;
    }

    /**
     * @return string
     */
    public function escFieldsForInsert(array $fields)
    {
        return sprintf('(`%s`)', implode('`,`', $fields));
    }

    /**
     * @return string
     */
    public function escValuesForInsert(array $values)
    {
        $values = array_values(array_map('esc_sql', $values));
        return sprintf("('%s')", implode("','", $values));
    }

    /**
     * @param string $statement
     * @return string
     */
    public function sql($statement)
    {
        $handle = $this->sqlHandle();
        $statement = glsr()->filterString('database/sql/'.$handle, $statement);
        glsr()->action('database/sql/'.$handle, $statement);
        glsr()->action('database/sql', $statement, $handle);
        return $statement;
    }

    /**
     * @return string
     */
    public function sqlJoin()
    {
        $join = $this->clauses('join');
        $join = glsr()->filterArrayUnique('query/sql/join', $join, $this->sqlHandle(), $this);
        return implode(' ', $join);
    }

    /**
     * @return string
     */
    public function sqlLimit()
    {
        $limit = Helper::ifTrue($this->args['per_page'] > 0,
            $this->db->prepare('LIMIT %d', $this->args['per_page'])
        );
        return glsr()->filterString('query/sql/limit', $limit, $this->sqlHandle(), $this);
    }

    /**
     * @return string
     */
    public function sqlOffset()
    {
        $offsetBy = (($this->args['page'] - 1) * $this->args['per_page']) + $this->args['offset'];
        $offset = Helper::ifTrue($offsetBy > 0,
            $this->db->prepare('OFFSET %d', $offsetBy)
        );
        return glsr()->filterString('query/sql/offset', $offset, $this->sqlHandle(), $this);
    }

    /**
     * @return string|void
     */
    public function sqlOrderBy()
    {
        $values = [
            'random' => 'RAND()',
        ];
        $order = $this->args['order'];
        $orderby = $this->args['orderby'];
        $orderedby = [];
        if (Str::startsWith(['p.', 'r.'], $orderby)) {
            $orderedby[] = "r.is_pinned {$order}";
            $orderedby[] = "{$orderby} {$order}";
        } elseif (array_key_exists($orderby, $values)) {
            $orderedby[] = $values[$orderby];
        }
        $orderedby = glsr()->filterArrayUnique('query/sql/order-by', $orderedby, $this->sqlHandle(), $this);
        if (!empty($orderedby)) {
            return 'ORDER BY '.implode(', ', $orderedby);
        }
    }

    /**
     * @return string
     */
    public function sqlWhere()
    {
        $and = $this->clauses('and');
        $and = glsr()->filterArrayUnique('query/sql/and', $and, $this->sqlHandle(), $this);
        return 'WHERE 1=1 '.implode(' ', $and);
    }

    /**
     * @return string
     */
    public function table($table)
    {
        return glsr(SqlSchema::class)->table($table);
    }

    /**
     * @return string
     */
    protected function clauseAndAssignedPosts()
    {
        $clauses = [];
        if ($postIds = $this->args['assigned_posts']) {
            $clauses[] = sprintf('(apt.post_id IN (%s) AND apt.is_published = 1)', implode(',', $postIds));
        }
        if ($termIds = $this->args['assigned_terms']) {
            $clauses[] = sprintf('(att.term_id IN (%s))', implode(',', $termIds));
        }
        if ($userIds = $this->args['assigned_users']) {
            $clauses[] = sprintf('(aut.user_id IN (%s))', implode(',', $userIds));
        }
        $operator = glsr()->filterString('query/sql/clause/operator', 'OR', $clauses, $this->args);
        $operator = strtoupper($operator);
        $operator = Helper::ifTrue(in_array($operator, ['AND', 'OR']), $operator, 'OR');
        if ($clauses = implode(" {$operator} ", $clauses)) {
            return "AND ($clauses)";
        }
        return '';
    }

    /**
     * @return string
     */
    protected function clauseAndDate()
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

    /**
     * @return string
     */
    protected function clauseAndEmail()
    {
        return Helper::ifTrue(!empty($this->args['email']),
            $this->db->prepare('AND r.email = %s', $this->args['email'])
        );
    }

    /**
     * @return string
     */
    protected function clauseAndIpAddress()
    {
        return Helper::ifTrue(!empty($this->args['ip_address']),
            $this->db->prepare('AND r.ip_address = %s', $this->args['ip_address'])
        );
    }

    /**
     * @return string
     */
    protected function clauseAndPostIn()
    {
        return Helper::ifTrue(!empty($this->args['post__in']),
            $this->db->prepare('AND r.review_id IN (%s)', implode(',', $this->args['post__in']))
        );
    }

    /**
     * @return string
     */
    protected function clauseAndPostNotIn()
    {
        return Helper::ifTrue(!empty($this->args['post__not_in']),
            $this->db->prepare('AND r.review_id NOT IN (%s)', implode(',', $this->args['post__not_in']))
        );
    }

    /**
     * @return string
     */
    protected function clauseAndRating()
    {
        return Helper::ifTrue(!empty($this->args['rating']),
            $this->db->prepare('AND r.rating > %d', --$this->args['rating'])
        );
    }

    /**
     * @return string
     */
    protected function clauseAndStatus()
    {
        return Helper::ifTrue(!Helper::isEmpty($this->args['status']),
            $this->db->prepare('AND r.is_approved = %d', $this->args['status'])
        );
    }

    /**
     * @return string
     */
    protected function clauseAndType()
    {
        return Helper::ifTrue(!empty($this->args['type']),
            $this->db->prepare('AND r.type = %s', $this->args['type'])
        );
    }

    /**
     * @return string
     */
    protected function clauseAndUserIn()
    {
        return Helper::ifTrue(!empty($this->args['user__in']),
            $this->db->prepare('AND p.post_author IN (%s)', implode(',', $this->args['user__in']))
        );
    }

    /**
     * @return string
     */
    protected function clauseAndUserNotIn()
    {
        return Helper::ifTrue(!empty($this->args['user__not_in']),
            $this->db->prepare('AND p.post_author NOT IN (%s)', implode(',', $this->args['user__not_in']))
        );
    }

    /**
     * @return string
     */
    protected function clauseJoinAssignedPosts()
    {
        return Helper::ifTrue(!empty($this->args['assigned_posts']),
            "INNER JOIN {$this->table('assigned_posts')} AS apt ON r.ID = apt.rating_id"
        );
    }

    /**
     * @return string
     */
    protected function clauseJoinAssignedTerms()
    {
        return Helper::ifTrue(!empty($this->args['assigned_terms']),
            "INNER JOIN {$this->table('assigned_terms')} AS att ON r.ID = att.rating_id"
        );
    }

    /**
     * @return string
     */
    protected function clauseJoinAssignedUsers()
    {
        return Helper::ifTrue(!empty($this->args['assigned_users']),
            "INNER JOIN {$this->table('assigned_users')} AS aut ON r.ID = aut.rating_id"
        );
    }

    /**
     * @return string
     */
    protected function clauseJoinDate()
    {
        return Helper::ifTrue(!empty(array_filter($this->args['date'])),
            "INNER JOIN {$this->db->posts} AS p ON r.review_id = p.ID"
        );
    }

    /**
     * @return string
     */
    protected function clauseJoinUserIn()
    {
        return Helper::ifTrue(!empty($this->args['user__in']),
            "INNER JOIN {$this->db->posts} AS p ON r.review_id = p.ID"
        );
    }

    /**
     * @return string
     */
    protected function clauseJoinUserNotIn()
    {
        return Helper::ifTrue(!empty($this->args['user__not_in']),
            "INNER JOIN {$this->db->posts} AS p ON r.review_id = p.ID"
        );
    }

    /**
     * @return string
     */
    protected function clauseJoinOrderBy()
    {
        return Helper::ifTrue(Str::startsWith('p.', $this->args['orderby']),
            "INNER JOIN {$this->db->posts} AS p ON r.review_id = p.ID"
        );
    }

    /**
     * @param int $depth
     * @return string
     */
    protected function sqlHandle($depth = 2)
    {
        return Str::dashCase(Arr::get((new \Exception())->getTrace(), $depth.'.function'));
    }
}
