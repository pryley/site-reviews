<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Defaults\ReviewsDefaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Helpers\Url;
use GeminiLabs\SiteReviews\Review;
use WP_Post;
use WP_Query;

class Query
{
    public $args;
    public $db;

    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
    }

    /**
     * @param int $reviewId
     * @return object|false
     */
    public function rating($reviewId)
    {
        $rating = $this->db->get_row(
            $this->db->prepare("SELECT * FROM {$this->getTable('ratings')} WHERE review_id = %d", $reviewId)
        );
        if (is_object($rating)) {
            $rating->post_ids = $this->ratingPivot('post_id', 'assigned_posts', $rating->ID);
            $rating->term_ids = $this->ratingPivot('term_id', 'assigned_terms', $rating->ID);
            $rating->user_ids = $this->ratingPivot('user_id', 'assigned_users', $rating->ID);
            return $rating;
        }
        return false;
    }

    /**
     * @param string $field
     * @param string $table
     * @param int $reviewId
     * @return array
     */
    public function ratingPivot($field, $table, $ratingId)
    {
        return $this->db->get_col(
            $this->db->prepare("SELECT {$field} FROM {$this->getTable($table)} WHERE rating_id = %d", 
                $ratingId
            )
        );
    }

    /**
     * @return array
     */
    public function ratings(array $args)
    {
        $this->setArgs($args);
        $join = implode(' ', $this->getSqlClauses([], 'join'));
        $and = implode(' ', $this->getSqlClauses([], 'and'));
        $ratings = array_fill_keys($this->ratingTypes(), []);
        foreach ($ratings as $type => &$values) {
            $this->args['type'] = $type;
            $values = $this->db->get_results("
                SELECT r.rating AS rating, COUNT(r.rating) AS count
                FROM {$this->getTable('ratings')} AS r {$join}
                WHERE r.is_approved = 1 {$and}
                GROUP BY rating
            ", ARRAY_A);
        }
        return $ratings;
    }

    /**
     * @return array
     */
    public function ratingTypes()
    {
        return in_array($this->args['type'], ['', 'all'])
            ? $this->db->get_col("SELECT type FROM {$this->getTable('ratings')} WHERE is_approved = 1 GROUP BY type")
            : [$this->args['type']];
    }

    /**
     * @return object
     */
    public function reviews(array $args = [])
    {
        $this->setArgs($args);
        $results = $this->db->get_results("
            {$this->getSqlSelect()}
            {$this->getSqlFrom()}
            {$this->getSqlJoin()}
            {$this->getSqlWhere()}
            {$this->getSqlGroupBy()}
            {$this->getSqlOrderBy()}
            {$this->getSqlLimit()}
            {$this->getSqlOffset()}
        ");
        $posts = $this->generatePosts($results);
        $total = $this->totalReviews($this->args);
        return (object) [
            'max_num_pages' => ceil($total / $this->args['per_page']),
            'results' => $this->generateReviews($posts),
            'total' => count($posts),
        ];
    }

    /**
     * @return int
     */
    public function totalReviews(array $args = [])
    {
        $this->setArgs($args);
        $result = $this->db->get_var("
            SELECT COUNT(*)
            {$this->getSqlFrom()}
            {$this->getSqlJoin()}
            {$this->getSqlWhere()}
        ");
        return absint($result);
    }

    /**
     * @param string $fromUrl
     * @return int
     */
    public function getPaged($fromUrl = null)
    {
        $pagedQueryVar = glsr()->constant('PAGED_QUERY_VAR');
        $pageNum = empty($fromUrl)
            ? filter_input(INPUT_GET, $pagedQueryVar, FILTER_VALIDATE_INT)
            : filter_var(Url::query($fromUrl, $pagedQueryVar), FILTER_VALIDATE_INT);
        if (empty($pageNum)) {
            $pageNum = (int) Arr::get($this->args, 'page', 1);
        }
        return max(1, $pageNum);
    }

    /**
     * @return string
     */
    public function getSqlFrom()
    {
        $from = "FROM {$this->db->posts} p";
        $from = glsr()->filterString('query/sql/from', $from, $this);
        return $from;
    }

    /**
     * @return string
     */
    public function getSqlGroupBy()
    {
        $groupBy = "GROUP BY p.ID";
        return glsr()->filterString('query/sql/group-by', $groupBy, $this);
    }

    /**
     * @return string
     */
    public function getSqlJoin()
    {
        $join = [
            "INNER JOIN {$this->getTable('ratings')} AS r ON p.ID = r.review_id",
        ];
        $join = $this->getSqlClauses($join, 'join');
        $join = glsr()->filterArray('query/sql/join', $join, $this);
        return implode(' ', $join);
    }

    /**
     * @return string
     */
    public function getSqlLimit()
    {
        $limit = $this->args['per_page'] > 0
            ? $this->db->prepare("LIMIT %d", $this->args['per_page'])
            : '';
        return glsr()->filterString('query/sql/limit', $limit, $this);
    }

    /**
     * @return string
     */
    public function getSqlOffset()
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
    public function getSqlOrderBy()
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
    public function getSqlSelect()
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
    public function getSqlWhere()
    {
        $where = [
            $this->db->prepare("AND p.post_type = '%s'", glsr()->post_type),
            "AND p.post_status = 'publish'",
        ];
        $where = $this->getSqlClauses($where, 'and');
        $where = glsr()->filterArray('query/sql/where', $where, $this);
        $where = implode(' ', $where);
        return "WHERE 1=1 {$where}";
    }

    /**
     * @return string
     */
    public function getTable($table)
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
            ? "INNER JOIN {$this->getTable('assigned_posts')} AS ap ON r.ID = ap.rating_id"
            : '';
    }

    /**
     * @return string
     */
    protected function clauseJoinCategory()
    {
        return !empty($this->args['category'])
            ? "INNER JOIN {$this->getTable('assigned_terms')} AS at ON r.ID = at.rating_id"
            : '';
    }

    /**
     * @return string
     */
    protected function clauseJoinUser()
    {
        return !empty($this->args['user'])
            ? "INNER JOIN {$this->getTable('assigned_users')} AS au ON r.ID = au.rating_id"
            : '';
    }

    /**
     * @return array
     */
    protected function generatePosts(array $results)
    {
        $posts = array_map('get_post', $results);
        if (!wp_using_ext_object_cache()) {
            update_post_caches($posts, glsr()->post_type);
        }
        return $posts;
    }

    /**
     * @return array
     */
    protected function generateReviews(array $posts)
    {
        $reviews = array_map([glsr(ReviewManager::class), 'single'], $posts);
        $postIds = array_unique(call_user_func_array('array_merge', glsr_array_column($reviews, 'post_ids')));
        $termIds = array_unique(call_user_func_array('array_merge', glsr_array_column($reviews, 'term_ids')));
        update_postmeta_cache($postIds);
        $lazyloader = wp_metadata_lazyloader();
        $lazyloader->queue_objects('term', $termIds); // term_ids for each review
        return $reviews;
    }

    /**
     * @param string $clause
     * @return array
     */
    protected function getSqlClauses(array $values, $clause)
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
     * @return array
     */
    protected function normalizeArgs(array $args = [])
    {
        $args = glsr(ReviewsDefaults::class)->merge($args);
        $args['assigned_to'] = Arr::uniqueInt(Arr::consolidate($args['assigned_to']));
        $args['category'] = $this->normalizeTermIds(Arr::consolidate($args['category']));
        $args['offset'] = absint(filter_var($args['offset'], FILTER_SANITIZE_NUMBER_INT));
        $args['order'] = Str::restrictTo('ASC,DESC,', sanitize_key($args['order']), 'DESC'); // include an empty value
        $args['orderby'] = $this->normalizeOrderBy($args['orderby']);
        $args['page'] = absint($args['page']);
        $args['per_page'] = absint($args['per_page']); // "0" and "-1" = all
        $args['post__in'] = Arr::uniqueInt(Arr::consolidate($args['post__in']));
        $args['post__not_in'] = Arr::uniqueInt(Arr::consolidate($args['post__not_in']));
        $args['rating'] = absint(filter_var($args['rating'], FILTER_SANITIZE_NUMBER_INT));
        $args['type'] = sanitize_key($args['type']);
        $args['user'] = $this->normalizeUserIds(Arr::consolidate($args['user']));
        return $args;
    }

    /**
     * @return string
     */
    protected function normalizeOrderBy($orderBy)
    {
        $orderBy = Str::restrictTo('author,comment_count,date,ID,menu_order,none,rand,relevance', $orderBy, 'date');
        if (in_array($orderBy, ['comment_count', 'ID', 'menu_order'])) {
            return Str::prefix('p.', $orderBy);
        }
        if (in_array($orderBy, ['author', 'date'])) {
            return Str::prefix('p.post_', $orderBy);
        }
        return $orderBy;
    }

    /**
     * @return array
     */
    protected function normalizeTermIds(array $terms)
    {
        $termIds = [];
        foreach ($terms as $termId) {
            if (!is_numeric($termId)) {
                $term = term_exists($termId, glsr()->taxonomy); // get the term from a term slug
                $termId = Arr::get($term, 'term_id', 0);
            }
            $termIds[] = $termId;
        }
        return Arr::uniqueInt($termIds);
    }

    /**
     * @return array
     */
    protected function normalizeUserIds(array $users)
    {
        $userIds = [];
        foreach ($users as $userId) {
            if (!is_numeric($userId)) {
                $userId = Helper::castToInt(username_exists($userId));
            }
            $userIds[] = $userId;
        }
        return Arr::uniqueInt($userIds);
    }

    /**
     * @return void
     */
    protected function setArgs(array $args = [])
    {
        $this->args = $this->normalizeArgs($args);
    }
}
