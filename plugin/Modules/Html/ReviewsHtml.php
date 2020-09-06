<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use ArrayObject;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Defaults\SiteReviewsDefaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Reviews;

class ReviewsHtml extends ArrayObject
{
    /**
     * @var \GeminiLabs\SiteReviews\Arguments
     */
    public $args;

    /**
     * @var int
     */
    public $max_num_pages;

    /**
     * @var array
     */
    public $reviews;

    public function __construct(Reviews $reviews, array $args)
    {
        $this->args = glsr()->args($args);
        $this->max_num_pages = $reviews->max_num_pages;
        $this->reviews = $this->renderReviews($reviews);
        parent::__construct($this->reviews, ArrayObject::STD_PROP_LIST|ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return glsr(Template::class)->build('templates/reviews', [
            'args' => $this->args,
            'context' => [
                'assigned_to' => $this->args->assigned_posts,
                'category' => $this->args->assigned_terms,
                'class' => $this->getClass(),
                'id' => '', // @deprecated in v5.0
                'pagination' => Helper::ifTrue(!empty($this->args->pagination), $this->getPagination()),
                'reviews' => $this->getReviews(),
            ],
        ]);
    }

    /**
     * @param bool $wrap
     * @return string
     */
    public function getPagination($wrap = true)
    {
        $html = glsr(Partial::class)->build('pagination', [
            'baseUrl' => $this->args->pageUrl,
            'current' => $this->args->page,
            'total' => $this->max_num_pages,
        ]);
        if (!$wrap) {
            return $html;
        }
        $ajaxClass = Helper::ifTrue('ajax' == $this->args->pagination, 'glsr-ajax-pagination');
        return glsr(Builder::class)->div($html, [
            'class' => trim('glsr-pagination '.$ajaxClass),
            'data-id' => $this->args->id,
            'data-paginate' => '',
        ]);
    }

    /**
     * @param bool $wrap
     * @return string
     */
    public function getReviews($wrap = true)
    {
        $html = empty($this->reviews)
            ? $this->getReviewsFallback()
            : implode(PHP_EOL, $this->reviews);
        if (!$wrap) {
            return $html;
        }
        return glsr(Builder::class)->div($html, wp_parse_args($this->getDataAttributes(), [
            'class' => 'glsr-reviews-list',
        ]));
    }

    /**
     * @param mixed $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        if (array_key_exists($key, $this->reviews)) {
            return $this->reviews[$key];
        }
        if (in_array($key, ['navigation', 'pagination'])) { // @deprecated in v5.0 (navigation)
            return $this->getPagination();
        }
        return property_exists($this, $key)
            ? $this->$key
            : null;
    }

    /**
     * @return string
     */
    protected function getClass()
    {
        $defaults = ['glsr-reviews'];
        $classes = explode(' ', $this->args->class);
        $classes = array_unique(array_merge($defaults, array_filter($classes)));
        return implode(' ', $classes);
    }

    /**
     * @return array
     */
    protected function getDataAttributes()
    {
        $attributes = [
            'data-id' => $this->args->id,
            'data-reviews' => '',
        ];
        if (Cast::toBool(glsr()->sessionGet('glsr_get_reviews', false))) { // Was the helper function used?
            $data = glsr(SiteReviewsDefaults::class)->dataAttributes($this->args->toArray());
            $attributes = wp_parse_args($attributes, $data);
        }
        return $attributes;
    }

    /**
     * @return string
     */
    protected function getReviewsFallback()
    {
        if (empty($this->args->fallback) && glsr(OptionManager::class)->getBool('settings.reviews.fallback')) {
            $this->args->fallback = __('There are no reviews yet. Be the first one to write one.', 'site-reviews');
        }
        $fallback = '<p class="glsr-no-margins">'.$this->args->fallback.'</p>';
        return glsr()->filterString('reviews/fallback', $fallback, $this->args->toArray());
    }

    /**
     * @return array
     */
    protected function renderReviews(Reviews $reviews)
    {
        $rendered = [];
        foreach ($reviews as $review) {
            $rendered[] = $review->build($this->args->toArray());
        }
        return $rendered;
    }
}
