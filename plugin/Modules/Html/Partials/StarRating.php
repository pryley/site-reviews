<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Partials;

use GeminiLabs\SiteReviews\Contracts\PartialContract;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Modules\Rating;

class StarRating implements PartialContract
{
    protected $prefix;
    protected $rating;

    /**
     * @return string
     */
    public function build(array $args = [])
    {
        $this->setProperties($args);
        $fullStars = intval(floor($this->rating));
        $halfStars = intval(ceil($this->rating - $fullStars));
        $emptyStars = max(0, glsr()->constant('MAX_RATING', Rating::class) - $fullStars - $halfStars);
        return glsr(Template::class)->build('templates/rating/stars', [
            'context' => [
                'empty_stars' => $this->getTemplate('empty-star', $emptyStars),
                'full_stars' => $this->getTemplate('full-star', $fullStars),
                'half_stars' => $this->getTemplate('half-star', $halfStars),
                'prefix' => $this->prefix,
                'title' => sprintf(__('%s rating', 'site-reviews'), number_format_i18n($this->rating, 1)),
            ],
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
            'context' => [
                'prefix' => $this->prefix,
            ],
        ]);
        return str_repeat($template, $timesRepeated);
    }

    /**
     * @return array
     */
    protected function setProperties(array $args)
    {
        $args = wp_parse_args($args, [
            'prefix' => glsr()->isAdmin() ? '' : 'glsr-',
            'rating' => 0,
        ]);
        $this->prefix = $args['prefix'];
        $this->rating = (float) str_replace(',', '.', $args['rating']);
    }
}
