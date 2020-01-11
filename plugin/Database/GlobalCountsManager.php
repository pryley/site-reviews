<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Modules\Rating;
use GeminiLabs\SiteReviews\Review;

class GlobalCountsManager
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
     * @return array
     */
    public function build()
    {
        return $this->manager->buildCounts();
    }

    /**
     * @return void
     */
    public function decrease(Review $review)
    {
        $this->update($this->manager->decreaseRating(
            $this->get(),
            $review->review_type,
            $review->rating
        ));
    }

    /**
     * @return array
     */
    public function get()
    {
        $counts = glsr(OptionManager::class)->get('counts', []);
        if (!is_array($counts)) {
            glsr_log()->error('Review counts is not an array; possibly due to incorrectly imported reviews.')->debug($counts);
            return [];
        }
        return $counts;
    }

    /**
     * @return void
     */
    public function increase(Review $review)
    {
        if (empty($counts = $this->get())) {
            $counts = $this->build();
        }
        $this->update($this->manager->increaseRating($counts, $review->review_type, $review->rating));
    }

    /**
     * @return void
     */
    public function update(array $reviewCounts)
    {
        glsr(OptionManager::class)->set('counts', $reviewCounts);
    }

    /**
     * @return void
     */
    public function updateAll()
    {
        $this->update($this->build());
    }
}
