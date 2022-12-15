<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Modules\Rating;

class SummaryRatingTag extends SummaryTag
{
    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        if (!$this->isHidden()) {
            $rating = glsr(Rating::class)->average($this->ratings);
            $rating = (string) number_format_i18n($rating, 1);
            return $this->wrap($rating, 'span');
        }
    }
}
