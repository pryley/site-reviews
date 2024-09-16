<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Helpers\Str;

class ExportManager
{
    protected Arguments $args;

    public function customHeader(Arguments $args): array
    {
        $this->args = $args;
        $sql = glsr(Query::class)->sql("
            SELECT DISTINCT pm.meta_key
            FROM table|postmeta AS pm
            INNER JOIN table|posts AS p ON (p.ID = pm.post_id)
            {$this->sqlWhere()}
            AND pm.meta_key LIKE '_custom_%%'
        ");
        $fieldnames = glsr(Database::class)->dbGetCol($sql);
        $fieldnames = array_map(fn ($name) => Str::removePrefix($name, '_'), $fieldnames);
        natsort($fieldnames);
        return array_values($fieldnames);
    }

    public function export(Arguments $args): array
    {
        $this->args = $args;
        if ('id' === $args->assigned_posts) {
            return $this->exportWithIds();
        }
        if ('slug' === $args->assigned_posts) {
            return $this->exportWithSlugs();
        }
        return [];
    }

    protected function exportWithIds(): array
    {
        $sql = glsr(Query::class)->sql($this->sqlAssignedIds());
        return (array) glsr(Database::class)->dbGetResults($sql, 'ARRAY_A');
    }

    protected function exportWithSlugs(): array
    {
        $sql = glsr(Query::class)->sql($this->sqlAssignedSlugs());
        return (array) glsr(Database::class)->dbGetResults($sql, 'ARRAY_A');
    }

    protected function sqlAssignedIds(): string
    {
        return "
            SELECT
                p.ID,
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
                GROUP_CONCAT(DISTINCT aut.user_id) AS assigned_users
            FROM table|ratings AS r
            INNER JOIN table|posts AS p ON (p.ID = r.review_id)
            LEFT JOIN table|assigned_posts AS apt ON (apt.rating_id = r.ID)
            LEFT JOIN table|assigned_terms AS att ON (att.rating_id = r.ID)
            LEFT JOIN table|assigned_users AS aut ON (aut.rating_id = r.ID)
            {$this->sqlWhere()}
            GROUP BY r.ID
            {$this->sqlLimit()}
        ";
    }

    protected function sqlAssignedSlugs(): string
    {
        return "
            SELECT
                p.ID,
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
                GROUP_CONCAT(DISTINCT CONCAT(ap.post_type, ':', ap.post_name)) AS assigned_posts,
                GROUP_CONCAT(DISTINCT att.term_id) AS assigned_terms,
                GROUP_CONCAT(DISTINCT aut.user_id) AS assigned_users
            FROM table|ratings AS r
            INNER JOIN table|posts AS p ON (p.ID = r.review_id)
            LEFT JOIN table|assigned_posts AS apt ON (apt.rating_id = r.ID)
            LEFT JOIN table|assigned_terms AS att ON (att.rating_id = r.ID)
            LEFT JOIN table|assigned_users AS aut ON (aut.rating_id = r.ID)
            LEFT JOIN table|posts AS ap ON (ap.ID = apt.post_id)
            {$this->sqlWhere()}
            GROUP BY r.ID
            {$this->sqlLimit()}
        ";
    }

    protected function sqlLimit(): string
    {
        global $wpdb;
        if ($limit = $this->args->cast('limit', 'int', 1000)) {
            return $wpdb->prepare('LIMIT %d', $limit);
        }
        return '';
    }

    protected function sqlWhere(): string
    {
        global $wpdb;
        $date = $this->args->sanitize('date', 'date');
        $postId = $this->args->cast('post_id', 'int', 0);
        $postStatus = Str::restrictTo(['pending', 'publish'], $this->args->cast('post_status', 'string'),
            "pending','publish"
        );
        $where = [
            "WHERE 1=1",
        ];
        if (!empty($postId)) {
            $where[] = $wpdb->prepare('AND p.ID > %d', $postId);
        }
        $where[] = $wpdb->prepare('AND p.post_type = %s', glsr()->post_type);
        $where[] = "AND p.post_status IN ('{$postStatus}')";
        if (!empty($date)) {
            $where[] = $wpdb->prepare('AND p.post_date > %s', $date);
        }
        return implode(' ', $where);
    }
}
