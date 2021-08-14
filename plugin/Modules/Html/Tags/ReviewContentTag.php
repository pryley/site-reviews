<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Text;

class ReviewContentTag extends ReviewTag
{
    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        if (!$this->isHidden()) {
            return $this->wrap($this->textExcerpt($value), 'p');
        }
    }

    protected function textExcerpt($value)
    {
        $limit = Cast::toInt(glsr_get_option('reviews.excerpts_length', 55));
        return glsr_get_option('reviews.excerpts', false, 'bool')
            ? Text::excerpt($value, $limit)
            : Text::text($value);
    }
}
