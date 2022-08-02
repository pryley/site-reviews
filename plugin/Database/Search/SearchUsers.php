<?php

namespace GeminiLabs\SiteReviews\Database\Search;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;

class SearchUsers extends AbstractSearch
{
    /**
     * @return string
     */
    public function render()
    {
        return array_reduce($this->results, function ($carry, $result) {
            return $carry.glsr()->build('partials/editor/search-result', [
                'ID' => $result->id,
                'permalink' => esc_url(get_author_posts_url($result->id)),
                'title' => esc_attr($result->name.' ('.$result->login.')'),
            ]);
        });
    }

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
        $sql = $this->db->prepare("
            SELECT u.ID AS id, u.user_login AS login, u.display_name AS name
            FROM {$this->db->users} u
            WHERE 1=1
            AND u.ID = %d
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
        $like = '%'.$this->db->esc_like($searchTerm).'%';
        $sql = $this->db->prepare("
            SELECT u.ID AS id, u.user_login AS login, u.display_name AS name
            FROM {$this->db->users} u
            WHERE 1=1
            AND (u.user_login LIKE %s OR u.display_name LIKE %s)
            ORDER BY u.display_name LIKE %s DESC
            LIMIT 0, 25
        ", $like, $like, $like);
        return glsr(Database::class)->dbGetResults(
            glsr(Query::class)->sql($sql)
        );
    }
}
