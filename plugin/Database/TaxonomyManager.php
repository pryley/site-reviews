<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;

class TaxonomyManager
{
    /**
     * @param int|string $termId
     * @return int
     */
    public function normalizeId($termId)
    {
        if (is_numeric($termId)) {
            $termId = Cast::toInt($termId);
        }
        $term = term_exists($termId, glsr()->taxonomy);
        return Cast::toInt(Arr::get($term, 'term_id'));
    }

    /**
     * @param array|string $termIds
     * @return array
     */
    public function normalizeIds($termIds)
    {
        $termIds = Cast::toArray($termIds);
        foreach ($termIds as &$termId) {
            $termId = $this->normalizeId($termId);
        }
        return Arr::uniqueInt($termIds);
    }

    /**
     * @param int $postId
     * @param array $termIds
     * @return void
     */
    public function setTerms($postId, $termIds)
    {
        $termIds = $this->normalizeIds($termIds);
        if (empty($termIds)) {
            return;
        }
        $termTaxonomyIds = wp_set_object_terms($postId, $termIds, glsr()->taxonomy);
        if (is_wp_error($termTaxonomyIds)) {
            glsr_log()->error($termTaxonomyIds->get_error_message());
        }
    }
}
