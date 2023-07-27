<?php

namespace GeminiLabs\SiteReviews\Database\Search;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Helpers\Str;

class SearchPosts extends AbstractSearch
{
    /**
     * @return array
     */
    public function posts()
    {
        $posts = [];
        foreach ($this->results as $result) {
            $posts[] = get_post($result->id);
        }
        return $posts;
    }

    /**
     * @return string
     */
    public function render()
    {
        return array_reduce($this->posts(), function ($carry, $post) {
            return $carry.glsr()->build('partials/editor/search-result', [
                'ID' => $post->ID,
                'permalink' => esc_url((string) get_permalink($post->ID)),
                'title' => esc_attr(get_the_title($post->ID)),
            ]);
        });
    }

    /**
     * @return string
     */
    protected function postStatuses()
    {
        $statuses = ['publish'];
        $statuses = glsr()->filterArray('search/posts/post_status', $statuses, 'posts');
        return Str::join($statuses, true);
    }

    /**
     * @return string
     */
    protected function postTypes()
    {
        $types = array_keys(get_post_types([
            '_builtin' => false,
            'exclude_from_search' => false,
        ]));
        $types[] = 'post';
        $types[] = 'page';
        $types = glsr()->filterArray('search/posts/post_type', $types);
        return Str::join($types, true);
    }

    /**
     * @param int $searchId
     * @return array
     */
    protected function searchById($searchId)
    {
        $sql = $this->db->prepare("
            SELECT p.ID AS id, p.post_title AS name
            FROM {$this->db->posts} AS p
            WHERE 1=1
            AND p.ID = %d
            AND p.post_type IN ({$this->postTypes()})
            AND p.post_status IN ({$this->postStatuses()})
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
            SELECT p.ID AS id, p.post_title AS name
            FROM {$this->db->posts} AS p
            WHERE 1=1
            AND p.post_title LIKE %s
            AND p.post_type IN ({$this->postTypes()})
            AND p.post_status IN ({$this->postStatuses()})
            ORDER BY p.post_title LIKE %s DESC, p.post_date DESC
            LIMIT 0, 20
        ", $like, $like);
        return glsr(Database::class)->dbGetResults(
            glsr(Query::class)->sql($sql)
        );
    }
}
