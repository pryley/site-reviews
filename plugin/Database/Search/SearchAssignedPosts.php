<?php

namespace GeminiLabs\SiteReviews\Database\Search;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Helpers\Str;

class SearchAssignedPosts extends AbstractSearch
{
    public function posts(): array
    {
        $posts = [];
        foreach ($this->results as $result) {
            $posts[] = get_post($result->id);
        }
        return $posts;
    }

    protected function postStatuses(): string
    {
        $statuses = array_keys(get_post_stati([
            'protected' => true,
            'show_in_admin_all_list' => true,
        ]));
        $statuses[] = 'private';
        $statuses[] = 'publish';
        $statuses = glsr()->filterArray('search/posts/post_status', $statuses, 'assigned_posts');
        return Str::join($statuses, true);
    }

    protected function searchById(int $searchId): array
    {
        $assignedPosts = glsr(Query::class)->table('assigned_posts');
        $sql = "
            SELECT p.ID as id, p.post_title as name
            FROM {$this->db->posts} AS p
            INNER JOIN {$assignedPosts} AS ap ON ap.post_id = p.ID
            WHERE 1=1
            AND ap.post_id = %d
            AND p.post_status IN ({$this->postStatuses()})
            GROUP BY p.ID
        ";
        return (array) glsr(Database::class)->dbGetResults(
            glsr(Query::class)->sql($sql, $searchId)
        );
    }

    protected function searchByTerm(string $searchTerm): array
    {
        $assignedPosts = glsr(Query::class)->table('assigned_posts');
        $like = '%'.$this->db->esc_like($searchTerm).'%';
        $sql = "
            SELECT p.ID as id, p.post_title as name
            FROM {$this->db->posts} AS p
            INNER JOIN {$assignedPosts} AS ap ON ap.post_id = p.ID
            WHERE 1=1
            AND p.post_title LIKE %s
            AND p.post_status IN ({$this->postStatuses()})
            GROUP BY p.ID
            ORDER BY p.post_title LIKE %s DESC, p.post_date DESC
            LIMIT 20
        ";
        return (array) glsr(Database::class)->dbGetResults(
            glsr(Query::class)->sql($sql, $like, $like)
        );
    }
}
