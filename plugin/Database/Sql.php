<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;

trait Sql
{
    public $args;
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
                $values[] = call_user_func([$this, $method]);
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
     * @param string $handle
     * @return string
     */
    public function sql($statement)
    {
        $e = new \Exception();
        $handle = Str::dashCase(Arr::get($e->getTrace(), '1.function'));
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
        $join = glsr()->filterArrayUnique('query/sql/join', $join, $this);
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
        return glsr()->filterString('query/sql/limit', $limit, $this);
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
    public function sqlWhere()
    {
        $and = $this->clauses('and');
        $and = glsr()->filterArrayUnique('query/sql/and', $and, $this);
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
            $clauses[] = $this->db->prepare('(apt.post_id IN (%s) AND apt.is_published = 1)', implode(',', $postIds));
        }
        if ($termIds = $this->args['assigned_terms']) {
            $clauses[] = $this->db->prepare('(att.term_id IN (%s))', implode(',', $termIds));
        }
        if ($userIds = $this->args['assigned_users']) {
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
    protected function clauseAndAuthorId()
    {
        return Helper::ifTrue(!empty($this->args['author_id']),
            $this->db->prepare('AND p.post_author = %d', $this->args['author_id'])
        );
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
    protected function clauseJoinAuthorId()
    {
        return Helper::ifTrue(!empty($this->args['author_id']),
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
}
