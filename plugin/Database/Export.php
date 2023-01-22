<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Sanitizer;

class Export
{
    protected $assignedPostsTable;
    protected $assignedTermsTable;
    protected $assignedUsersTable;
    protected $db;
    protected $ratingsTable;

    public function __construct()
    {
        global $wpdb;
        $this->assignedPostsTable = glsr(Query::class)->table('assigned_posts');
        $this->assignedTermsTable = glsr(Query::class)->table('assigned_terms');
        $this->assignedUsersTable = glsr(Query::class)->table('assigned_users');
        $this->db = $wpdb;
        $this->ratingsTable = glsr(Query::class)->table('ratings');
    }

    /**
     * @return array
     */
    public function export(array $args = [])
    {
        return glsr(Database::class)->dbGetResults($this->sqlAssignedIds($args), 'ARRAY_A');
    }

    /**
     * @return array
     */
    public function exportWithSlugs(array $args = [])
    {
        return glsr(Database::class)->dbGetResults($this->sqlAssignedSlugs($args), 'ARRAY_A');
    }

    /**
     * @return string
     */
    protected function sqlAssignedIds(array $args)
    {
        return glsr(Query::class)->sql("
            SELECT
                p.post_date AS date,
                p.post_date_gmt AS date_gmt,
                p.post_title AS title,
                p.post_content AS content,
                r.rating,
                r.name,
                r.email,
                r.avatar,
                r.ip_address,
                r.is_approved,
                r.is_pinned,
                r.is_verified,
                r.score,
                r.terms,
                GROUP_CONCAT(DISTINCT apt.post_id) AS assigned_posts,
                GROUP_CONCAT(DISTINCT att.term_id) AS assigned_terms,
                GROUP_CONCAT(DISTINCT aut.user_id) AS assigned_users,
                GROUP_CONCAT(DISTINCT pm.meta_value) as response
            FROM {$this->ratingsTable} AS r
            INNER JOIN {$this->db->posts} AS p ON r.review_id = p.ID
            LEFT JOIN {$this->assignedPostsTable} AS apt ON r.ID = apt.rating_id
            LEFT JOIN {$this->assignedTermsTable} AS att ON r.ID = att.rating_id
            LEFT JOIN {$this->assignedUsersTable} AS aut ON r.ID = aut.rating_id
            LEFT JOIN {$this->db->postmeta} AS pm ON (r.review_id = pm.post_id AND pm.meta_key = '_response')
            {$this->where($args)}
            GROUP BY r.ID
        ");
    }

    /**
     * @return string
     */
    protected function sqlAssignedSlugs(array $args)
    {
        return glsr(Query::class)->sql("
            SELECT
                p.post_date AS date,
                p.post_date_gmt AS date_gmt,
                p.post_title AS title,
                p.post_content AS content,
                r.rating,
                r.name,
                r.email,
                r.avatar,
                r.ip_address,
                r.is_approved,
                r.is_pinned,
                r.is_verified,
                r.score,
                r.terms,
                GROUP_CONCAT(DISTINCT CONCAT(p1.post_type, ':', p1.post_name)) AS assigned_posts,
                GROUP_CONCAT(DISTINCT att.term_id) AS assigned_terms,
                GROUP_CONCAT(DISTINCT aut.user_id) AS assigned_users,
                GROUP_CONCAT(DISTINCT pm.meta_value) as response
            FROM {$this->ratingsTable} AS r
            INNER JOIN {$this->db->posts} AS p ON r.review_id = p.ID
            LEFT JOIN {$this->assignedPostsTable} AS apt ON r.ID = apt.rating_id
            LEFT JOIN {$this->assignedTermsTable} AS att ON r.ID = att.rating_id
            LEFT JOIN {$this->assignedUsersTable} AS aut ON r.ID = aut.rating_id
            LEFT JOIN {$this->db->posts} AS p1 ON apt.post_id = p1.ID
            LEFT JOIN {$this->db->postmeta} AS pm ON (r.review_id = pm.post_id AND pm.meta_key = '_response')
            {$this->where($args)}
            GROUP BY r.ID
        ");
    }

    /**
     * @return string
     */
    protected function where(array $args)
    {
        $date = glsr(Sanitizer::class)->sanitizeDate(Arr::get($args, 'date'));
        $status = Str::restrictTo('pending,publish', Arr::get($args, 'post_status'), "pending','publish");
        $where = [
            $this->db->prepare('WHERE p.post_type = %s', glsr()->post_type),
            sprintf("AND p.post_status IN ('%s')", $status),
        ];
        if (!empty($date)) {
            $where[] = $this->db->prepare('AND p.post_date > %s', $date);
        }
        return implode(' ', $where);
    }
}
