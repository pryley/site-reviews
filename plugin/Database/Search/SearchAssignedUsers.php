<?php

namespace GeminiLabs\SiteReviews\Database\Search;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;

class SearchAssignedUsers extends AbstractSearch
{
    public function users(): array
    {
        $users = [];
        foreach ($this->results as $result) {
            $users[] = get_user_by('id', $result->id);
        }
        return $users;
    }

    protected function searchById(int $searchId): array
    {
        $sql = "
            SELECT 
                u.ID AS id,
                u.user_login AS login,
                u.display_name AS name,
                u.user_nicename AS nickname
            FROM table|users u
            INNER JOIN table|assigned_users AS ap ON (ap.user_id = u.ID)
            WHERE 1=1
            AND ap.user_id = %d
            GROUP BY u.ID
        ";
        return (array) glsr(Database::class)->dbGetResults(
            glsr(Query::class)->sql($sql, $searchId)
        );
    }

    protected function searchByTerm(string $searchTerm): array
    {
        $like = '%'.$this->db->esc_like($searchTerm).'%';
        $sql = "
            SELECT 
                u.ID AS id,
                u.user_login AS login,
                u.display_name AS name,
                u.user_nicename AS nickname
            FROM table|users u
            INNER JOIN table|assigned_users AS ap ON (ap.user_id = u.ID)
            WHERE 1=1
            AND (u.user_login LIKE %s OR u.display_name LIKE %s OR u.user_nicename LIKE %s)
            GROUP BY u.ID
            ORDER BY u.display_name LIKE %s DESC
            LIMIT 20
        ";
        return (array) glsr(Database::class)->dbGetResults(
            glsr(Query::class)->sql($sql, $like, $like, $like, $like)
        );
    }
}
