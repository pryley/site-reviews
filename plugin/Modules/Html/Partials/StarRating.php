<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Partials;

use GeminiLabs\SiteReviews\Contracts\PartialContract;
use GeminiLabs\SiteReviews\Defaults\StarRatingDefaults;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Modules\Rating;

class StarRating implements PartialContract
{
    /**
     * @var \GeminiLabs\SiteReviews\Arguments
     */
    public $data;

    /**
     * {@inheritdoc}
     */
    public function build(array $data = [])
    {
        $this->data = glsr()->args(glsr(StarRatingDefaults::class)->merge($data));
        $maxRating = glsr()->constant('MAX_RATING', Rating::class);
        $fullStars = intval(floor($this->data->rating));
        $halfStars = intval(ceil($this->data->rating - $fullStars));
        $emptyStars = max(0, $maxRating - $fullStars - $halfStars);
        $title = $this->data->count > 0
            ? __('Rated <strong>%s</strong> out of %s based on %s ratings', 'site-reviews')
            : __('Rated <strong>%s</strong> out of %s', 'site-reviews');
        return glsr(Template::class)->build('templates/rating/stars', [
            'args' => glsr()->args($this->data->args),
            'context' => [
                'empty_stars' => $this->getTemplate('empty-star', $emptyStars),
                'full_stars' => $this->getTemplate('full-star', $fullStars),
                'half_stars' => $this->getTemplate('half-star', $halfStars),
                'prefix' => $this->data->prefix,
                'title' => sprintf($title, $this->data->rating, $maxRating, $this->data->count),
            ],
            'partial' => $this,
        ]);
    }

    /**
     * @param string $templateName
     * @param int $timesRepeated
     * @return string
     */
    protected function getTemplate($templateName, $timesRepeated)
    {
        $template = glsr(Template::class)->build('templates/rating/'.$templateName, [
            'args' => $this->data->args,
            'context' => [
                'prefix' => $this->data->prefix,
            ],
            'partial' => $this,
        ]);
        return str_repeat($template, $timesRepeated);
    }
}
