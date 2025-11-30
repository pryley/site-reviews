<?php

namespace GeminiLabs\SiteReviews\Integrations\Bricks\Commands;

use GeminiLabs\SiteReviews\Database;

class SearchAssignedPosts extends AbstractSearchCommand
{
    public function handle(): void
    {
        $this->verifyNonce();
        $args = [
            'post__in' => [],
            'posts_per_page' => 25,
        ];
        if (is_numeric($this->search)) {
            $args['post__in'][] = (int) $this->search;
        } else {
            $args['s'] = $this->search;
        }
        $results = glsr(Database::class)->posts($args);
        if ($missingIds = $this->missingIds($results, $this->include)) {
            $results += glsr(Database::class)->posts([
                'post__in' => $missingIds,
            ]);
        }
        $formatted = $this->formatResults($results);
        if (empty($this->search)) {
            $formatted = [
                'post_id' => esc_html_x('The Current Page', 'admin-text', 'site-reviews'),
                'parent_id' => esc_html_x('The Parent Page', 'admin-text', 'site-reviews'),
            ] + $formatted;
        }
        $this->response = $formatted;
    }
}
