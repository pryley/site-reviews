<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Helpers\Arr;

class TaxonomyManager
{
    /**
     * @param array[]|string $termIds
     * @return array
     */
    public function normalizeTermIds($termIds)
    {
        $termIds = Arr::convertFromString($termIds);
        foreach ($termIds as &$termId) {
            $term = term_exists($termId, glsr()->taxonomy); // get the term from a term slug
            $termId = Arr::get($term, 'term_id', 0);
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
        $termIds = $this->normalizeTermIds($termIds);
        if (empty($termIds)) {
            return;
        }
        $termTaxonomyIds = wp_set_object_terms($postId, $termIds, glsr()->taxonomy);
        if (is_wp_error($termTaxonomyIds)) {
            glsr_log()->error($termTaxonomyIds->get_error_message());
        }
    }
}
