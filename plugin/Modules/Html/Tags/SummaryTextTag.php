<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Modules\Rating;

class SummaryTextTag extends SummaryTag
{
    protected function handle(): string
    {
        if ($this->isHidden()) {
            return '';
        }
        return $this->wrap($this->value(), 'span');
    }

    protected function value(): string
    {
        $max = glsr()->constant('MAX_RATING', Rating::class);
        $num = glsr(Rating::class)->totalCount($this->ratings);
        $rating = glsr(Rating::class)->average($this->ratings);
        $rating = glsr(Rating::class)->format($rating);
        $text = $this->args->text;
        if (empty($text)) {
            $text = _nx(
                '{rating} out of {max} stars (based on {num} review)',
                '{rating} out of {max} stars (based on {num} reviews)',
                $num,
                'Do not translate {rating}, {max}, and {num}, they are template tags.',
                'site-reviews'
            );
        }
        $num = number_format_i18n($num);
        return str_replace(['{rating}', '{max}', '{num}'], [$rating, $max, $num], $text);
    }
}
