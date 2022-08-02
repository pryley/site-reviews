<?php

namespace GeminiLabs\SiteReviews\Database\Search;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;

class SearchAssignedUsers extends AbstractSearch
{
    /**
     * @return array
     */
    public function users()
    {
        $users = [];
        foreach ($this->results as $result) {
            $users[] = get_user_by('ID', $result->id);
        }
        return $users;
    }

    /**
     * @param int $searchId
     * @return array
     */
    protected function searchById($searchId)
    {
        $assignedUsers = glsr(Query::class)->table('assigned_users');
        $sql = $this->db->prepare("
            SELECT u.ID AS id, u.user_login AS login, u.display_name AS name
            FROM {$this->db->users} u
            INNER JOIN {$assignedUsers} AS ap ON ap.user_id = u.ID
            WHERE 1=1
            AND ap.user_id = %d
            GROUP BY u.ID
        ", $searchId);
        return glsr(Database::class)->dbGetResults(
            glsr(Query::class)->sql($sql)
        );
    }

    /**
     * @param string $searchTerm
     * @return array
     */
    protected function searchByTerm($searchTerm)
    {
        $assignedUsers = glsr(Query::class)->table('assigned_users');
        $like = '%'.$this->db->esc_like($searchTerm).'%';
        $sql = $this->db->prepare("
            SELECT u.ID AS id, u.user_login AS login, u.display_name AS name
            FROM {$this->db->users} u
            INNER JOIN {$assignedUsers} AS ap ON ap.user_id = u.ID
            WHERE 1=1
            AND (u.user_login LIKE %s OR u.display_name LIKE %s)
            GROUP BY u.ID
            ORDER BY u.display_name LIKE %s DESC
            LIMIT 20
        ", $like, $like, $like);
        return glsr(Database::class)->dbGetResults(
            glsr(Query::class)->sql($sql)
        );
    }
}
