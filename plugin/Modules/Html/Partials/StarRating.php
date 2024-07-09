<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Partials;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Contracts\PartialContract;
use GeminiLabs\SiteReviews\Defaults\StarRatingDefaults;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Rating;

class StarRating implements PartialContract
{
    protected Arguments $data;

    public function build(array $args = []): string
    {
        $this->data = glsr()->args(
            glsr(StarRatingDefaults::class)->restrict($args)
        );
        return glsr(Builder::class)->div([
            'aria-label' => $this->label(),
            'class' => 'glsr-star-rating glsr-stars',
            'data-rating' => $this->data->rating,
            'data-reviews' => $this->data->reviews,
            'role' => 'img',
            'text' => $this->stars(),
        ]);
    }

    protected function label()
    {
        $maxRating = glsr()->constant('MAX_RATING', Rating::class);
        $rating = $this->data->rating;
        $title = $this->data->reviews > 0
            ? __('Rated %s out of %s stars based on %s ratings', 'site-reviews')
            : __('Rated %s out of %s stars', 'site-reviews');
        if (0 !== $this->data->num_half) {
            $rating = glsr(Rating::class)->format($rating);
        }
        return sprintf($title, $rating, $maxRating, $this->data->reviews);
    }

    protected function stars(): string
    {
        $types = [ // order is intentional
            'full' => $this->data->num_full,
            'half' => $this->data->num_half,
            'empty' => $this->data->num_empty,
        ];
        $results = [];
        foreach ($types as $type => $repeat) {
            $template = glsr(Builder::class)->span([
                'aria-hidden' => 'true',
                'class' => "glsr-star glsr-star-{$type}",
            ]);
            $results[] = str_repeat($template, $repeat);
        }
        return implode('', $results);
    }
}
