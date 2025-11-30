<?php

namespace GeminiLabs\SiteReviews\Integrations\Bricks\Commands;

use GeminiLabs\SiteReviews\Database;

class SearchAssignedTerms extends AbstractSearchCommand
{
    public function handle(): void
    {
        $this->verifyNonce();
        $results = glsr(Database::class)->terms([
            'number' => 25,
            'search' => $this->search,
        ]);
        if ($missingIds = $this->missingIds($results, $this->include)) {
            $results += glsr(Database::class)->terms([
                'term_taxonomy_id' => $missingIds,
            ]);
        }
        $this->response = $this->formatResults($results);
    }
}
