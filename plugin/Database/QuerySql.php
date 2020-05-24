<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Database\SqlSchema;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Str;

trait QuerySql
{
    public $args;
    public $db;

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
        $from = "FROM {$this->db->posts} p";
        $from = glsr()->filterString('query/sql/from', $from, $this);
        return $from;
    }

    /**
     * @return string
     */
    public function sqlGroupBy()
    {
        $groupBy = "GROUP BY p.ID";
        return glsr()->filterString('query/sql/group-by', $groupBy, $this);
    }

    /**
     * @return string
     */
    public function sqlJoin()
    {
        $join = [
            "INNER JOIN {$this->table('ratings')} AS r ON p.ID = r.review_id",
        ];
        $join = $this->sqlClauses($join, 'join');
        $join = glsr()->filterArray('query/sql/join', $join, $this);
        return implode(' ', $join);
    }

    /**
     * @return string
     */
    public function sqlLimit()
    {
        $limit = $this->args['per_page'] > 0
            ? $this->db->prepare("LIMIT %d", $this->args['per_page'])
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
            ? $this->db->prepare("OFFSET %d", $offsetBy)
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
            'rand' => "ORDER BY RAND()",
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
            'p.*', 'r.rating', 'r.type', 'r.is_pinned',
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
            $this->db->prepare("AND p.post_type = '%s'", glsr()->post_type),
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
     * This takes care of both assigned_to and category
     * @return string
     */
    protected function clauseAndAssignedTo()
    {
        $clauses = [];
        if ($postIds = $this->args['assigned_to']) {
            $clauses[] = $this->db->prepare("(ap.post_id IN (%s) AND ap.is_published = 1)", implode(',', $postIds));
        }
        if ($termIds = $this->args['category']) {
            $clauses[] = $this->db->prepare("(at.term_id IN (%s))", implode(',', $termIds));
        }
        if ($userIds = $this->args['user']) {
            $clauses[] = $this->db->prepare("(au.user_id IN (%s))", implode(',', $userIds));
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
            ? $this->db->prepare("AND r.rating > %d", --$this->args['rating'])
            : '';
    }

    /**
     * @return string
     */
    protected function clauseAndType()
    {
        return $this->args['type']
            ? $this->db->prepare("AND r.type = '%s'", $this->args['type'])
            : '';
    }

    /**
     * @return string
     */
    protected function clauseJoinAssignedTo()
    {
        return !empty($this->args['assigned_to'])
            ? "INNER JOIN {$this->table('assigned_posts')} AS ap ON r.ID = ap.rating_id"
            : '';
    }

    /**
     * @return string
     */
    protected function clauseJoinCategory()
    {
        return !empty($this->args['category'])
            ? "INNER JOIN {$this->table('assigned_terms')} AS at ON r.ID = at.rating_id"
            : '';
    }

    /**
     * @return string
     */
    protected function clauseJoinUser()
    {
        return !empty($this->args['user'])
            ? "INNER JOIN {$this->table('assigned_users')} AS au ON r.ID = au.rating_id"
            : '';
    }
}
