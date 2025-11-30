<?php

namespace GeminiLabs\SiteReviews\Integrations\Bricks\Commands;

use GeminiLabs\SiteReviews\Database;

class SearchPostId extends AbstractSearchCommand
{
    public function handle(): void
    {
        $this->verifyNonce();
        $args = [
            'post__in' => [],
            'post_type' => glsr()->post_type,
            'posts_per_page' => 25,
        ];
        if (is_numeric($this->search)) {
            $args['post__in'][] = (int) $this->search;
        } else {
            $args['s'] = (string) $this->search;
        }
        $results = glsr(Database::class)->posts($args);
        if ($missingIds = $this->missingIds($results, $this->include)) {
            $results += glsr(Database::class)->posts([
                'post__in' => $missingIds,
                'post_type' => glsr()->post_type,
            ]);
        }
        $this->response = $this->formatResults($results);
    }
}
