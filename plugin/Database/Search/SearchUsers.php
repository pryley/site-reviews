<?php

namespace GeminiLabs\SiteReviews\Database\Search;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;

class SearchUsers extends AbstractSearch
{
    public function render(): string
    {
        return array_reduce($this->results, function ($carry, $result) {
            return $carry.glsr()->build('partials/editor/search-result', [
                'ID' => $result->id,
                'permalink' => esc_url(get_author_posts_url($result->id)),
                'title' => esc_attr("{$result->name} ({$result->login})"),
            ]);
        }, '');
    }

    public function users(): array
    {
        $users = [];
        foreach ($this->results as $result) {
            $users[] = get_user_by('ID', $result->id);
        }
        return $users;
    }

    protected function searchById(int $searchId): array
    {
        $sql = "
            SELECT u.ID AS id, u.user_login AS login, u.display_name AS name
            FROM table|users u
            WHERE 1=1
            AND u.ID = %d
        ";
        return (array) glsr(Database::class)->dbGetResults(
            glsr(Query::class)->sql($sql, $searchId)
        );
    }

    protected function searchByTerm(string $searchTerm): array
    {
        $like = '%'.$this->db->esc_like($searchTerm).'%';
        $sql = "
            SELECT u.ID AS id, u.user_login AS login, u.display_name AS name
            FROM table|users u
            WHERE 1=1
            AND (u.user_login LIKE %s OR u.display_name LIKE %s)
            ORDER BY u.display_name LIKE %s DESC
            LIMIT 0, 25
        ";
        return (array) glsr(Database::class)->dbGetResults(
            glsr(Query::class)->sql($sql, $like, $like, $like)
        );
    }
}
