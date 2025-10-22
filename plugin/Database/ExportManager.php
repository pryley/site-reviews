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
            WHERE {$this->sqlWhere()}
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
        $sql = glsr(Query::class)->sql("
            SELECT {$this->sqlSelect()}
            FROM table|ratings AS r
            {$this->sqlJoin()}
            WHERE {$this->sqlWhere()}
            GROUP BY r.ID
            {$this->sqlLimit()}
        ");
        return (array) glsr(Database::class)->dbGetResults($sql, 'ARRAY_A');
    }

    protected function sqlJoin(): string
    {
        $join = [
            "INNER JOIN table|posts AS p ON (p.ID = r.review_id)",
            "LEFT JOIN table|assigned_posts AS apt ON (apt.rating_id = r.ID)",
            "LEFT JOIN table|assigned_terms AS att ON (att.rating_id = r.ID)",
            "LEFT JOIN table|assigned_users AS aut ON (aut.rating_id = r.ID)",
        ];
        if ('slug' === $this->args->author_id) {
            $join[] = "LEFT JOIN table|users AS authors ON (authors.ID = p.post_author)";
        }
        if ('slug' === $this->args->assigned_posts) {
            $join[] = "LEFT JOIN table|posts AS posts ON (posts.ID = apt.post_id)";
        }
        if ('slug' === $this->args->assigned_terms) {
            $join[] = "LEFT JOIN table|terms AS terms ON (terms.term_id = att.term_id)";
        }
        if ('slug' === $this->args->assigned_users) {
            $join[] = "LEFT JOIN table|users AS users ON (users.ID = aut.user_id)";
        }
        return implode(' ', $join);
    }

    protected function sqlLimit(): string
    {
        global $wpdb;
        if ($limit = $this->args->cast('limit', 'int', 1000)) {
            return $wpdb->prepare('LIMIT %d', $limit);
        }
        return '';
    }

    protected function sqlSelect(): string
    {
        $select = [
            "p.ID",
            "p.post_date AS date",
            "p.post_date_gmt AS date_gmt",
            "p.post_title AS title",
            "p.post_content AS content",
            "r.rating",
            "r.name",
            "r.email",
            "r.avatar",
            "r.ip_address",
            "r.is_approved",
            "r.is_pinned",
            "r.is_verified",
            "r.score",
            "r.terms",
        ];
        $select[] = 'slug' === $this->args->author_id
            ? "authors.user_login AS author_id"
            : "p.post_author AS author_id";
        $select[] = 'slug' === $this->args->assigned_posts
            ? "GROUP_CONCAT(DISTINCT CONCAT(posts.post_type, ':', posts.post_name)) AS assigned_posts"
            : "GROUP_CONCAT(DISTINCT apt.post_id) AS assigned_posts";
        $select[] = 'slug' === $this->args->assigned_terms
            ? "GROUP_CONCAT(DISTINCT terms.slug) AS assigned_terms"
            : "GROUP_CONCAT(DISTINCT att.term_id) AS assigned_terms";
        $select[] = 'slug' === $this->args->assigned_users
            ? "GROUP_CONCAT(DISTINCT users.user_login) AS assigned_users"
            : "GROUP_CONCAT(DISTINCT aut.user_id) AS assigned_users";
        return implode(', ', $select);
    }

    protected function sqlWhere(): string
    {
        global $wpdb;
        $date = $this->args->sanitize('date', 'date');
        $postId = $this->args->cast('post_id', 'int', 0);
        $postStatus = Str::restrictTo(['pending', 'publish'], $this->args->cast('post_status', 'string'),
            "pending','publish"
        );
        $where = ["1=1"];
        if (!empty($postId)) {
            $where[] = $wpdb->prepare('p.ID > %d', $postId);
        }
        $where[] = $wpdb->prepare('p.post_type = %s', glsr()->post_type);
        $where[] = "p.post_status IN ('{$postStatus}')";
        if (!empty($date)) {
            $where[] = $wpdb->prepare('p.post_date > %s', $date);
        }
        return implode(' AND ', $where);
    }
}
