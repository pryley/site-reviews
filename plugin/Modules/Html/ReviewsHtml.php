<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use ArrayObject;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helpers\Arr;

class ReviewsHtml extends ArrayObject
{
    /**
     * @var array
     */
    public $args;

    /**
     * @var int
     */
    public $max_num_pages;

    /**
     * @var string
     */
    public $pagination;

    /**
     * @var array
     */
    public $reviews;

    public function __construct(array $renderedReviews, $maxPageCount, array $args)
    {
        $this->args = $args;
        $this->max_num_pages = $maxPageCount;
        $this->reviews = $renderedReviews;
        $this->pagination = $this->buildPagination();
        parent::__construct($renderedReviews, ArrayObject::STD_PROP_LIST | ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return glsr(Template::class)->build('templates/reviews', [
            'args' => $this->args,
            'context' => [
                'assigned_to' => $this->args['assigned_to'],
                'category' => $this->args['category'],
                'class' => $this->getClass(),
                'id' => $this->args['id'],
                'pagination' => $this->getPagination(),
                'reviews' => $this->getReviews(),
            ],
        ]);
    }

    /**
     * @return string
     */
    public function getPagination()
    {
        return wp_validate_boolean($this->args['pagination'])
            ? $this->pagination
            : '';
    }

    /**
     * @return string
     */
    public function getReviews()
    {
        $html = empty($this->reviews)
            ? $this->getReviewsFallback()
            : implode(PHP_EOL, $this->reviews);
        $wrapper = '<div class="glsr-reviews">%s</div>';
        $wrapper = apply_filters('site-reviews/reviews/reviews-wrapper', $wrapper);
        return sprintf($wrapper, $html);
    }

    /**
     * @param mixed $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        if ('navigation' == $key) {
            glsr()->deprecated[] = 'The $reviewsHtml->navigation property has been been deprecated. Please use the $reviewsHtml->pagination property instead.';
            return $this->pagination;
        }
        if (array_key_exists($key, $this->reviews)) {
            return $this->reviews[$key];
        }
        return property_exists($this, $key)
            ? $this->$key
            : null;
    }

    /**
     * @return string
     */
    protected function buildPagination()
    {
        $html = glsr(Partial::class)->build('pagination', [
            'baseUrl' => Arr::get($this->args, 'pagedUrl'),
            'current' => Arr::get($this->args, 'paged'),
            'total' => $this->max_num_pages,
        ]);
        $html.= sprintf('<glsr-pagination hidden data-atts=\'%s\'></glsr-pagination>', $this->args['json']);
        $wrapper = '<div class="glsr-pagination">%s</div>';
        $wrapper = apply_filters('site-reviews/reviews/pagination-wrapper', $wrapper);
        return sprintf($wrapper, $html);
    }

    /**
     * @return string
     */
    protected function getClass()
    {
        $defaults = [
            'glsr-default',
        ];
        if ('ajax' == $this->args['pagination']) {
            $defaults[] = 'glsr-ajax-pagination';
        }
        $classes = explode(' ', $this->args['class']);
        $classes = array_unique(array_merge($defaults, array_filter($classes)));
        return implode(' ', $classes);
    }

    /**
     * @return string
     */
    protected function getReviewsFallback()
    {
        if (empty($this->args['fallback']) && glsr(OptionManager::class)->getBool('settings.reviews.fallback')) {
            $this->args['fallback'] = __('There are no reviews yet. Be the first one to write one.', 'site-reviews');
        }
        $fallback = '<p class="glsr-no-margins">'.$this->args['fallback'].'</p>';
        return apply_filters('site-reviews/reviews/fallback', $fallback, $this->args);
    }
}
