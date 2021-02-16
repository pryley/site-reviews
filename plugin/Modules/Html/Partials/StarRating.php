<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Partials;

use GeminiLabs\SiteReviews\Contracts\PartialContract;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Modules\Rating;

class StarRating implements PartialContract
{
    /**
     * @var \GeminiLabs\SiteReviews\Arguments
     */
    protected $args;

    /**
     * @var int
     */
    protected $count;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * @var float
     */
    protected $rating;

    /**
     * {@inheritdoc}
     */
    public function build(array $data = [])
    {
        $this->setProperties($data);
        $maxRating = glsr()->constant('MAX_RATING', Rating::class);
        $fullStars = intval(floor($this->rating));
        $halfStars = intval(ceil($this->rating - $fullStars));
        $emptyStars = max(0, $maxRating - $fullStars - $halfStars);
        $title = $this->count > 0
            ? __('Rated <strong>%s</strong> out of %s based on %s ratings', 'site-reviews')
            : __('Rated <strong>%s</strong> out of %s', 'site-reviews');
        return glsr(Template::class)->build('templates/rating/stars', [
            'args' => $this->args,
            'context' => [
                'empty_stars' => $this->getTemplate('empty-star', $emptyStars),
                'full_stars' => $this->getTemplate('full-star', $fullStars),
                'half_stars' => $this->getTemplate('half-star', $halfStars),
                'prefix' => $this->prefix,
                'title' => sprintf($title, $this->rating, $maxRating, $this->count),
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
            'args' => $this->args,
            'context' => [
                'prefix' => $this->prefix,
            ],
        ]);
        return str_repeat($template, $timesRepeated);
    }

    /**
     * @return void
     */
    protected function setProperties(array $data)
    {
        $data = wp_parse_args($data, [
            'args' => [],
            'count' => 0,
            'prefix' => glsr()->isAdmin() ? '' : 'glsr-',
            'rating' => 0,
        ]);
        $this->args = glsr()->args($data['args']);
        $this->count = (int) $data['count'];
        $this->prefix = $data['prefix'];
        $this->rating = (float) sprintf('%g', $data['rating']); // remove unnecessary trailing zeros
    }
}
