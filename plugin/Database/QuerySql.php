<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Str;

trait QuerySql
{
    public $args;
    public $db;

    public function escFieldsForInsert(array $fields)
    {
        return sprintf('(`%s`)', implode('`,`', $fields));
    }

    public function escValuesForInsert(array $values)
    {
        $values = array_map('esc_sql', $values);
        return sprintf("('%s')", implode("','", array_values($values)));
    }

    /**
     * @param string $clause
     * @return array
     */
    public function sqlClauses(array $values, $clause)
    {
        $prefix = Str::restrictTo('and, join', $clause);
        foreach (array_keys($this->args) as $key) {
            $method = Helper::buildMethodName($key, 'clause-'.$prefix);
            if (method_exists($this, $method)) {
                $values[] = call_user_func([$this, $method]);
            }
        }
        return $values;
    }

    /**
     * @return string
     */
    public function sqlFrom()
    {
        $from = "FROM {$this->table('ratings')} r";
        $from = glsr()->filterString('query/sql/from', $from, $this);
        return $from;
    }

    /**
     * @return string
     */
    public function sqlGroupBy()
    {
        $groupBy = 'GROUP BY p.ID';
        return glsr()->filterString('query/sql/group-by', $groupBy, $this);
    }

    /**
     * @return string
     */
    public function sqlJoin()
    {
        $join = [
            "INNER JOIN {$this->db->posts} AS p ON r.review_id = p.ID",
        ];
        $join = glsr()->filterArray('query/sql/join', $join, $this);
        return implode(' ', $join);
    }

    /**
     * @return string
     */
    public function sqlJoinClauses()
    {
        $join = $this->sqlClauses([], 'join');
        $join = glsr()->filterArray('query/sql/join-clauses', $join, $this);
        return trim($this->sqlJoin().' '.implode(' ', $join));
    }

    /**
     * @return string
     */
    public function sqlJoinPivots()
    {
        $join = [
            "LEFT JOIN {$this->table('assigned_posts')} apt on r.ID = apt.rating_id",
            "LEFT JOIN {$this->table('assigned_terms')} att on r.ID = att.rating_id",
            "LEFT JOIN {$this->table('assigned_users')} aut on r.ID = aut.rating_id",
        ];
        $join = glsr()->filterArray('query/sql/join-pivots', $join, $this);
        return implode(' ', $join);
    }

    /**
     * @return string
     */
    public function sqlLimit()
    {
        $limit = $this->args['per_page'] > 0
            ? $this->db->prepare('LIMIT %d', $this->args['per_page'])
            : '';
        return glsr()->filterString('query/sql/limit', $limit, $this);
    }

    /**
     * @return string
     */
    public function sqlOffset()
    {
        $offsetBy = (($this->args['page'] - 1) * $this->args['per_page']) + $this->args['offset'];
        $offset = ($offsetBy > 0)
            ? $this->db->prepare('OFFSET %d', $offsetBy)
            : '';
        return glsr()->filterString('query/sql/offset', $offset, $this);
    }

    /**
     * @return string
     */
    public function sqlOrderBy()
    {
        $values = [
            'none' => '',
            'rand' => 'ORDER BY RAND()',
            'relevance' => '',
        ];
        $order = $this->args['order'];
        $orderby = $this->args['orderby'];
        if (Str::startsWith('p.', $orderby)) {
            $orderBy = "ORDER BY r.is_pinned {$order}, {$orderby} {$order}";
        } elseif (array_key_exists($orderby, $values)) {
            $orderBy = $orderby;
        } else {
            $orderBy = '';
        }
        return glsr()->filterString('query/sql/order-by', $orderBy, $this);
    }

    /**
     * @return string
     */
    public function sqlSelect()
    {
        $select = [
            'r.*',
            'p.post_author as author_id',
            'p.post_date as date',
            'p.post_content as content',
            'p.post_title as title',
            'p.post_status as status',
            'GROUP_CONCAT(DISTINCT apt.post_id) as post_ids',
            'GROUP_CONCAT(DISTINCT att.term_id) as term_ids',
            'GROUP_CONCAT(DISTINCT aut.user_id) as user_ids',
        ];
        $select = glsr()->filterArray('query/sql/select', $select, $this);
        $select = implode(', ', $select);
        return "SELECT {$select}";
    }

    /**
     * @return string
     */
    public function sqlWhere()
    {
        $where = [
            $this->db->prepare('AND p.post_type = %s', glsr()->post_type),
            "AND p.post_status = 'publish'",
        ];
        $where = $this->sqlClauses($where, 'and');
        $where = glsr()->filterArray('query/sql/where', $where, $this);
        $where = implode(' ', $where);
        return "WHERE 1=1 {$where}";
    }

    /**
     * @return string
     */
    public function table($table)
    {
        return glsr(SqlSchema::class)->table($table);
    }

    /**
     * This takes care of assigned_to, category, and user.
     * @return string
     */
    protected function clauseAndAssignedTo()
    {
        $clauses = [];
        if ($postIds = $this->args['assigned_to']) {
            $clauses[] = $this->db->prepare('(apt.post_id IN (%s) AND apt.is_published = 1)', implode(',', $postIds));
        }
        if ($termIds = $this->args['category']) {
            $clauses[] = $this->db->prepare('(att.term_id IN (%s))', implode(',', $termIds));
        }
        if ($userIds = $this->args['user']) {
            $clauses[] = $this->db->prepare('(aut.user_id IN (%s))', implode(',', $userIds));
        }
        if ($clauses = implode(' OR ', $clauses)) {
            return "AND ($clauses)";
        }
        return '';
    }

    /**
     * @return string
     */
    protected function clauseAndRating()
    {
        return $this->args['rating']
            ? $this->db->prepare('AND r.rating > %d', --$this->args['rating'])
            : '';
    }

    /**
     * @return string
     */
    protected function clauseAndType()
    {
        return $this->args['type']
            ? $this->db->prepare('AND r.type = %s', $this->args['type'])
            : '';
    }

    /**
     * @return string
     */
    protected function clauseJoinAssignedTo()
    {
        return !empty($this->args['assigned_to'])
            ? "INNER JOIN {$this->table('assigned_posts')} AS apt ON r.ID = apt.rating_id"
            : '';
    }

    /**
     * @return string
     */
    protected function clauseJoinCategory()
    {
        return !empty($this->args['category'])
            ? "INNER JOIN {$this->table('assigned_terms')} AS att ON r.ID = att.rating_id"
            : '';
    }

    /**
     * @return string
     */
    protected function clauseJoinUser()
    {
        return !empty($this->args['user'])
            ? "INNER JOIN {$this->table('assigned_users')} AS aut ON r.ID = aut.rating_id"
            : '';
    }
}
