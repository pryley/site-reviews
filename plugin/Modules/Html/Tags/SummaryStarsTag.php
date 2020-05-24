<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Modules\Rating;

class SummaryStarsTag extends SummaryTag
{
    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        if (!$this->isHidden()) {
            $rating = glsr(Rating::class)->getAverage($this->ratings);
            return $this->wrap(glsr_star_rating($rating), 'span');
        }
    }
}
