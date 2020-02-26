<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Modules\Rating;
use GeminiLabs\SiteReviews\Review;

class TermCountsManager
{
    /**
     * @var CountsManager
     */
    protected $manager;

    public function __construct()
    {
        $this->manager = glsr(CountsManager::class);
    }

    /**
     * @param int $termTaxonomyId
     * @return array
     */
    public function build($termTaxonomyId)
    {
        return $this->manager->buildCounts([
            'term_ids' => [$termTaxonomyId],
        ]);
    }

    /**
     * @return void
     */
    public function decrease(Review $review)
    {
        foreach ($review->term_ids as $termId) {
            if (empty($counts = $this->get($termId))) {
                continue;
            }
            $this->update($termId,
                $this->manager->decreaseRating($counts, $review->review_type, $review->rating)
            );
        }
    }

    /**
     * @param int $termId
     * @return array
     */
    public function get($termId)
    {
        return array_filter((array) get_term_meta($termId, CountsManager::META_COUNT, true));
    }

    /**
     * @return void
     */
    public function increase(Review $review)
    {
        $terms = glsr(ReviewManager::class)->normalizeTerms(implode(',', $review->term_ids));
        foreach ($terms as $term) {
            $counts = $this->get($term['term_id']);
            $counts = empty($counts)
                ? $this->build($term['term_taxonomy_id'])
                : $this->manager->increaseRating($counts, $review->review_type, $review->rating);
            $this->update($term['term_id'], $counts);
        }
    }

    /**
     * @param int $termId
     * @return void
     */
    public function update($termId, array $reviewCounts)
    {
        $term = get_term($termId, Application::TAXONOMY);
        if (!isset($term->term_id)) {
            return;
        }
        $ratingCounts = $this->manager->flatten($reviewCounts);
        update_term_meta($termId, CountsManager::META_COUNT, $reviewCounts);
        update_term_meta($termId, CountsManager::META_AVERAGE, glsr(Rating::class)->getAverage($ratingCounts));
        update_term_meta($termId, CountsManager::META_RANKING, glsr(Rating::class)->getRanking($ratingCounts));
    }

    /**
     * @return void
     */
    public function updateAll()
    {
        glsr(SqlQueries::class)->deleteTermCountMetaKeys();
        $terms = glsr(Database::class)->getTerms([
            'fields' => 'all',
        ]);
        foreach ($terms as $term) {
            $this->update($term->term_id, $this->build($term->term_taxonomy_id));
        }
    }
}
