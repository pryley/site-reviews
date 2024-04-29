<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Sanitizer;

class Export
{
    protected $db;

    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
    }

    public function export(array $args = []): array
    {
        return (array) glsr(Database::class)->dbGetResults($this->sqlAssignedIds($args), 'ARRAY_A');
    }

    public function exportWithSlugs(array $args = []): array
    {
        return (array) glsr(Database::class)->dbGetResults($this->sqlAssignedSlugs($args), 'ARRAY_A');
    }

    protected function sqlAssignedIds(array $args): string
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
            FROM table|ratings AS r
            INNER JOIN table|posts AS p ON (p.ID = r.review_id)
            LEFT JOIN table|assigned_posts AS apt ON (apt.rating_id = r.ID)
            LEFT JOIN table|assigned_terms AS att ON (att.rating_id = r.ID)
            LEFT JOIN table|assigned_users AS aut ON (aut.rating_id = r.ID)
            LEFT JOIN table|postmeta AS pm ON (pm.post_id = r.review_id AND pm.meta_key = '_response')
            {$this->where($args)}
            GROUP BY r.ID
        ");
    }

    protected function sqlAssignedSlugs(array $args): string
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
            FROM table|ratings AS r
            INNER JOIN table|posts AS p ON (p.ID = r.review_id)
            LEFT JOIN table|assigned_posts AS apt ON (apt.rating_id = r.ID)
            LEFT JOIN table|assigned_terms AS att ON (att.rating_id = r.ID)
            LEFT JOIN table|assigned_users AS aut ON (aut.rating_id = r.ID)
            LEFT JOIN table|posts AS p1 ON (p1.ID = apt.post_id)
            LEFT JOIN table|postmeta AS pm ON (pm.post_id = r.review_id AND pm.meta_key = '_response')
            {$this->where($args)}
            GROUP BY r.ID
        ");
    }

    protected function where(array $args): string
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
