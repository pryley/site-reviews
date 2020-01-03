<?php

namespace GeminiLabs\SiteReviews;

use ArrayObject;
use GeminiLabs\SiteReviews\Defaults\SiteReviewsDefaults;
use GeminiLabs\SiteReviews\Modules\Html\Partials\SiteReviews as SiteReviewsPartial;
use GeminiLabs\SiteReviews\Modules\Html\ReviewsHtml;

class Reviews extends ArrayObject
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
     * @var array
     */
    public $reviews;

    public function __construct(array $reviews, $maxPageCount, array $args)
    {
        $this->args = $args;
        $this->max_num_pages = $maxPageCount;
        $this->reviews = $reviews;
        parent::__construct($reviews, ArrayObject::STD_PROP_LIST | ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->build();
    }

    /**
     * @return ReviewsHtml
     */
    public function build()
    {
        $args = glsr(SiteReviewsDefaults::class)->merge($this->args);
        return glsr(SiteReviewsPartial::class)->build($args, $this);
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
        return property_exists($this, $key)
            ? $this->$key
            : null;
    }

    /**
     * @return void
     */
    public function render()
    {
        echo $this->build();
    }
}
