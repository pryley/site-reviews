<?php

namespace GeminiLabs\SiteReviews;

use ArrayObject;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Defaults\SiteReviewsDefaults;
use GeminiLabs\SiteReviews\Modules\Html\Partials\SiteReviews;
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

    /**
     * @var int
     */
    public $total;

    public function __construct(array $reviews, $total, array $args)
    {
        $this->args = glsr(SiteReviewsDefaults::class)->merge($args);
        $this->max_num_pages = ceil($total / $this->args['per_page']);
        $this->reviews = $reviews;
        $this->total = $total;
        parent::__construct($this->reviews, ArrayObject::STD_PROP_LIST | ArrayObject::ARRAY_AS_PROPS);
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
        return glsr(SiteReviews::class)->build($this->args, $this);
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
