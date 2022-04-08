<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Database;

class Export
{
    protected $db;
    protected $results;

    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
        $this->results = [];
    }

    public function export()
    {
        $sql = sprintf("
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
                GROUP_CONCAT(DISTINCT aut.user_id) AS assigned_users
            FROM %s AS r
            INNER JOIN {$this->db->posts} AS p ON r.review_id = p.ID
            LEFT JOIN %s AS apt ON r.ID = apt.rating_id
            LEFT JOIN %s AS att ON r.ID = att.rating_id
            LEFT JOIN %s AS aut ON r.ID = aut.rating_id
            LEFT JOIN {$this->db->posts} AS p1 ON apt.post_id = p1.ID
            WHERE p.post_type = '%s'
            AND p.post_status IN ('publish','pending')
            GROUP BY r.ID
        ",
            glsr(Query::class)->table('ratings'),
            glsr(Query::class)->table('assigned_posts'),
            glsr(Query::class)->table('assigned_terms'),
            glsr(Query::class)->table('assigned_users'),
            glsr()->post_type
        );
        return glsr(Database::class)->dbGetResults(glsr(Query::class)->sql($sql), 'ARRAY_N');
    }

    public function exportWithIds()
    {
        $sql = sprintf("
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
                GROUP_CONCAT(DISTINCT aut.user_id) AS assigned_users
            FROM %s AS r
            INNER JOIN {$this->db->posts} AS p ON r.review_id = p.ID
            LEFT JOIN %s AS apt ON r.ID = apt.rating_id
            LEFT JOIN %s AS att ON r.ID = att.rating_id
            LEFT JOIN %s AS aut ON r.ID = aut.rating_id
            WHERE p.post_type = '%s'
            AND p.post_status IN ('publish','pending')
            GROUP BY r.ID
        ",
            glsr(Query::class)->table('ratings'),
            glsr(Query::class)->table('assigned_posts'),
            glsr(Query::class)->table('assigned_terms'),
            glsr(Query::class)->table('assigned_users'),
            glsr()->post_type
        );
        return glsr(Database::class)->dbGetResults(glsr(Query::class)->sql($sql), 'ARRAY_N');
    }
}
