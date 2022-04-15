<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Database;

class Export
{
    protected $assignedPostsTable;
    protected $assignedTermsTable;
    protected $assignedUsersTable;
    protected $db;
    protected $postType;
    protected $ratingsTable;

    public function __construct()
    {
        global $wpdb;
        $this->assignedPostsTable = glsr(Query::class)->table('assigned_posts');
        $this->assignedTermsTable = glsr(Query::class)->table('assigned_terms');
        $this->assignedUsersTable = glsr(Query::class)->table('assigned_users');
        $this->db = $wpdb;
        $this->postType = glsr()->post_type;
        $this->ratingsTable = glsr(Query::class)->table('ratings');
    }

    /**
     * @return array
     */
    public function export()
    {
        return glsr(Database::class)->dbGetResults($this->sqlAssignedIds(), 'ARRAY_A');
    }

    /**
     * @return array
     */
    public function exportWithSlugs()
    {
        return glsr(Database::class)->dbGetResults($this->sqlAssignedSlugs(), 'ARRAY_A');
    }

    /**
     * @return string
     */
    protected function sqlAssignedIds()
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
            WHERE p.post_type = '{$this->postType}'
            AND p.post_status IN ('publish','pending')
            GROUP BY r.ID
        ");
    }

    /**
     * @return string
     */
    protected function sqlAssignedSlugs()
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
            WHERE p.post_type = '{$this->postType}'
            AND p.post_status IN ('publish','pending')
            GROUP BY r.ID
        ");
    }
}
