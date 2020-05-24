<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\Date;

class ReviewDateTag extends ReviewTag
{
    /**
     * @param string $value
     * @return string
     */
    protected function formattedDate($value)
    {
        $dateFormat = glsr_get_option('reviews.date.format', 'default');
        if ('relative' == $dateFormat) {
            return glsr(Date::class)->relative($value);
        }
        $format = 'custom' == $dateFormat
            ? glsr_get_option('reviews.date.custom', 'M j, Y')
            : glsr(OptionManager::class)->getWP('date_format', 'F j, Y');
        return date_i18n($format, strtotime($value));
    }

    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        if (!$this->isHidden()) {
            return $this->wrap($this->formattedDate($value), 'span');
        }
    }
}
