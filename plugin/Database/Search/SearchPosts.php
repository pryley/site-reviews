<?php

namespace GeminiLabs\SiteReviews\Database\Search;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Helpers\Str;

class SearchPosts extends AbstractSearch
{
    public function posts(): array
    {
        $posts = [];
        foreach ($this->results as $result) {
            $posts[] = get_post($result->id);
        }
        return $posts;
    }

    public function render(): string
    {
        return array_reduce($this->posts(), function ($carry, $post) {
            return $carry.glsr()->build('partials/editor/search-result', [
                'ID' => $post->ID,
                'permalink' => esc_url((string) get_permalink($post->ID)),
                'title' => esc_attr(get_the_title($post->ID)),
            ]);
        }, '');
    }

    protected function postStatuses(): string
    {
        $statuses = ['publish'];
        $statuses = glsr()->filterArray('search/posts/post_status', $statuses, 'posts');
        return Str::join($statuses, true);
    }

    protected function postTypes(): string
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

    protected function searchById(int $searchId): array
    {
        $sql = "
            SELECT p.ID AS id, p.post_title AS name
            FROM table|posts AS p
            WHERE 1=1
            AND p.ID = %d
            AND p.post_type IN ({$this->postTypes()})
            AND p.post_status IN ({$this->postStatuses()})
        ";
        return (array) glsr(Database::class)->dbGetResults(
            glsr(Query::class)->sql($sql, $searchId)
        );
    }

    protected function searchByTerm(string $searchTerm): array
    {
        $like = '%'.$this->db->esc_like($searchTerm).'%';
        $sql = "
            SELECT p.ID AS id, p.post_title AS name
            FROM table|posts AS p
            WHERE 1=1
            AND p.post_title LIKE %s
            AND p.post_type IN ({$this->postTypes()})
            AND p.post_status IN ({$this->postStatuses()})
            ORDER BY p.post_title LIKE %s DESC, p.post_date DESC
            LIMIT 0, 20
        ";
        return (array) glsr(Database::class)->dbGetResults(
            glsr(Query::class)->sql($sql, $like, $like)
        );
    }
}
