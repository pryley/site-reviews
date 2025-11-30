<?php

namespace GeminiLabs\SiteReviews\Integrations\Bricks\Commands;

use GeminiLabs\SiteReviews\Database;

class SearchAssignedUsers extends AbstractSearchCommand
{
    public function handle(): void
    {
        $this->verifyNonce();
        $results = glsr(Database::class)->users([
            'number' => 25,
            'search_wild' => $this->search,
        ]);
        if ($missingIds = $this->missingIds($results, $this->include)) {
            $results += glsr(Database::class)->users([
                'include' => $missingIds,
            ]);
        }
        $formatted = $this->formatResults($results);
        if (empty($this->search)) {
            $formatted = [
                'user_id' => esc_html_x('The Logged In User', 'admin-text', 'site-reviews'),
                'author_id' => esc_html_x('The Page Author', 'admin-text', 'site-reviews'),
                'profile_id' => esc_html_x('The Profile User', 'admin-text', 'site-reviews'),
            ] + $formatted;
        }
        $this->response = $formatted;
    }
}
