<?php

namespace GeminiLabs\SiteReviews\Integrations\SASWP;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Schema;

class Controller extends AbstractController
{
    /**
     * @param array $data
     *
     * @filter saswp_modify_reviews_schema
     */
    public function filterSchema($data): array
    {
        $data = Arr::consolidate($data);
        $schema = glsr(Schema::class)->generate();
        if (empty($schema)) {
            return $data;
        }
        return glsr(Schema::class)->rate($data, $schema);
    }
}
